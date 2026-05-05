
@extends('layouts.ap')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ $titulo }}</h2>

    {{-- Botones de acción según permisos --}}
    <div class="mb-3">
        {{-- Nueva Visita - Residentes y Admins --}}
        @can('gestionar visitas')
            <a href="{{ route('visitas.create') }}" class="btn btn-primary">Nueva Visita</a>
        @endcan
        
        @can('administrar visitas')
            <a href="{{ route('visitas.create') }}" class="btn btn-primary">Nueva Visita</a>
        @endcan

        {{-- Panel Guardia - Solo porteros y admins --}}
        @can('operar porteria')
            <a href="{{ route('visitas.panel-guardia') }}" class="btn btn-secondary">Panel Guardia</a>
            <a href="{{ route('visitas.mostrar-validar-codigo') }}" class="btn btn-info">Validar Código</a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Buscador --}}
    <form method="GET" action="{{ route('visitas.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text"
                   name="search"
                   class="form-control"
                   placeholder="Buscar por código, visitante, CI o placa"
                   value="{{ request('search') }}">
            <button class="btn btn-outline-primary" type="submit">
                <i class="fas fa-search"></i> Buscar
            </button>
            @if(request('search'))
                <a href="{{ route('visitas.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            @endif
        </div>
    </form>

    {{-- Tabla de visitas --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Visitante</th>
                    <th>CI</th>
                    {{-- Mostrar columna Residente solo para admin/portero --}}
                    @canany(['administrar visitas', 'operar porteria'])
                        <th>Residente</th>
                    @endcanany
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Placa</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($visitas as $visita)
                @php
                    $rowClass = '';
                    if ($visita->estado == 'pendiente' && now() > $visita->fecha_fin) {
                        $rowClass = 'table-danger';
                    } elseif ($visita->estado == 'pendiente' && now() > \Carbon\Carbon::parse($visita->fecha_fin)->subMinutes(30)) {
                        $rowClass = 'table-warning';
                    }
                @endphp

                <tr class="{{ $rowClass }}">
                    <td>{{ $visita->id }}</td>
                    <td>
                        <strong class="text-primary">{{ $visita->codigo }}</strong>
                        {{-- Indicador de tiempo --}}
                        @if($visita->estado == 'pendiente')
                            @if(now() > $visita->fecha_fin)
                                <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Expirada</small>
                            @elseif(now() > \Carbon\Carbon::parse($visita->fecha_fin)->subMinutes(30))
                                <br><small class="text-warning"><i class="fas fa-clock"></i> Por expirar</small>
                            @elseif(now() < \Carbon\Carbon::parse($visita->fecha_inicio)->subMinutes(30))
                                <br><small class="text-info"><i class="fas fa-clock"></i> Muy temprano</small>
                            @elseif(now() < $visita->fecha_inicio)
                                <br><small class="text-success"><i class="fas fa-check"></i> Tolerancia OK</small>
                            @endif
                        @endif
                    </td>
                    <td>{{ $visita->nombre_visitante }}</td>
                    <td>{{ $visita->ci_visitante }}</td>
                    
                    {{-- Mostrar residente solo para admin/portero --}}
                    @canany(['administrar visitas', 'operar porteria'])
                        <td>
                            {{ $visita->residente ? $visita->residente->nombre_completo : '-' }}
                        </td>
                    @endcanany
                    
                    <td>{{ Str::limit($visita->motivo, 30) }}</td>
                    <td>
                        @switch($visita->estado)
                            @case('pendiente')
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock"></i> Pendiente
                                </span>
                                @break
                            @case('en_curso')
                                <span class="badge bg-info">
                                    <i class="fas fa-play"></i> En Curso
                                </span>
                                @break
                            @case('finalizada')
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> Finalizada
                                </span>
                                @break
                            @case('rechazada')
                                <span class="badge bg-danger">
                                    <i class="fas fa-times"></i> Rechazada
                                </span>
                                @break
                        @endswitch
                    </td>
                    <td>
                        <small>{{ \Carbon\Carbon::parse($visita->fecha_inicio)->format('d/m/Y') }}</small><br>
                        <strong>{{ \Carbon\Carbon::parse($visita->fecha_inicio)->format('H:i') }}</strong>
                    </td>
                    <td>
                        <small>{{ \Carbon\Carbon::parse($visita->fecha_fin)->format('d/m/Y') }}</small><br>
                        <strong>{{ \Carbon\Carbon::parse($visita->fecha_fin)->format('H:i') }}</strong>
                    </td>
                    <td>{{ $visita->placa_vehiculo ?? '-' }}</td>
                    <td>
                        <div class="btn-group-vertical btn-group-sm" role="group">
                            {{-- Ver detalles - Todos pueden --}}
                            <a href="{{ route('visitas.show', $visita->id) }}" 
                               class="btn btn-outline-info btn-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            
                            {{-- Acciones para visitas pendientes --}}
                            @if($visita->estado == 'pendiente')
                                
                                {{-- Editar - Residentes sus visitas, Admins todas --}}
                                @can('gestionar visitas')
                                    <a href="{{ route('visitas.edit', $visita) }}"
                                       class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                @endcan
                                
                                @can('administrar visitas')
                                    <a href="{{ route('visitas.edit', $visita) }}"
                                       class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                @endcan

                                {{-- Registrar entrada - Solo porteros/admins --}}
                                @can('operar porteria')
                                    <form action="{{ route('visitas.entrada', $visita) }}"
                                          method="POST" style="display:inline;">
                                        @csrf
                                        <button class="btn btn-outline-success btn-sm"
                                                onclick="return confirm('¿Registrar entrada de {{ $visita->nombre_visitante }}?')"
                                                @if(now() < \Carbon\Carbon::parse($visita->fecha_inicio)->subMinutes(30) || now() > $visita->fecha_fin) disabled @endif>
                                            <i class="fas fa-sign-in-alt"></i> Entrada
                                        </button>
                                    </form>
                                @endcan

                                {{-- Eliminar - Residentes/Admins --}}
                                @canany(['gestionar visitas', 'administrar visitas'])
                                    <form action="{{ route('visitas.destroy', $visita) }}"
                                          method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('¿Eliminar visita de {{ $visita->nombre_visitante }}?')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                @endcanany
                            @endif

                            {{-- Registrar salida - Solo porteros/admins --}}
                            @if($visita->estado == 'en_curso')
                                @can('operar porteria')
                                    <button type="button" class="btn btn-outline-primary btn-sm" 
                                            data-bs-toggle="modal" data-bs-target="#salidaModal{{ $visita->id }}">
                                        <i class="fas fa-sign-out-alt"></i> Salida
                                    </button>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>

                {{-- Modal para registrar salida --}}
                @if($visita->estado == 'en_curso')
                    @can('operar porteria')
                        <div class="modal fade" id="salidaModal{{ $visita->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('visitas.salida', $visita) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Registrar Salida</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Visitante:</strong> {{ $visita->nombre_visitante }}</p>
                                            <p><strong>CI:</strong> {{ $visita->ci_visitante }}</p>
                                            <div class="mb-3">
                                                <label class="form-label">Observaciones (opcional)</label>
                                                <textarea name="observaciones" class="form-control" rows="3"
                                                          placeholder="Ej: Salida normal, incidente, etc."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Registrar Salida</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endcan
                @endif

            @empty
                <tr>
                    @php
                        $colspan = auth()->user()->canAny(['administrar visitas', 'operar porteria']) ? 11 : 10;
                    @endphp
                    <td colspan="{{ $colspan }}" class="text-center py-4">
                        {{-- Mensaje cuando no hay visitas --}}
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p class="mb-0">
                                @can('gestionar visitas')
                                    No tienes visitas registradas.
                                @else
                                    No hay visitas registradas.
                                @endcan
                            </p>
                            @canany(['gestionar visitas', 'administrar visitas'])
                                <a href="{{ route('visitas.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Crear Primera Visita
                                </a>
                            @endcanany
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="d-flex justify-content-center">
        {{ $visitas->appends(['search' => request('search')])->links() }}
    </div>
</div>
@endsection