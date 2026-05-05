
{{-- resources/views/visitas/show.blade.php --}}
@extends('layouts.ap')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-eye text-primary"></i> Detalle de la Visita
                </h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('visitas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    @can('operar porteria')
                        <a href="{{ route('visitas.panel-guardia') }}" class="btn btn-outline-info">
                            <i class="fas fa-shield-alt"></i> Panel Guardia
                        </a>
                    @endcan
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle"></i> Información de la Visita
                                <span class="badge bg-primary ms-2">{{ $visita->codigo }}</span>
                            </h5>
                        </div>
                        <div class="col-md-4 text-end">
                            @switch($visita->estado)
                                @case('pendiente')
                                    <span class="badge bg-warning fs-6">
                                        <i class="fas fa-clock"></i> Pendiente
                                    </span>
                                    @break
                                @case('en_curso')
                                    <span class="badge bg-info fs-6">
                                        <i class="fas fa-play"></i> En Curso
                                    </span>
                                    @break
                                @case('finalizada')
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-check"></i> Finalizada
                                    </span>
                                    @break
                                @case('rechazada')
                                    <span class="badge bg-danger fs-6">
                                        <i class="fas fa-times"></i> Rechazada
                                    </span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        {{-- Información del Visitante --}}
                        <div class="col-md-6">
                            <div class="card bg-light h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user"></i> Datos del Visitante
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-hashtag text-primary"></i> Código de Visita:
                                        </label>
                                        <div class="fs-4 text-primary fw-bold">{{ $visita->codigo }}</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-id-card text-info"></i> Nombre:
                                        </label>
                                        <div>{{ $visita->nombre_visitante }}</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-id-badge text-info"></i> CI:
                                        </label>
                                        <div>{{ $visita->ci_visitante }}</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-comment text-warning"></i> Motivo:
                                        </label>
                                        <div>{{ $visita->motivo }}</div>
                                    </div>
                                    
                                    <div class="mb-0">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-car text-secondary"></i> Vehículo:
                                        </label>
                                        <div>{{ $visita->placa_vehiculo ?? 'Sin vehículo' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Información de la Visita --}}
                        <div class="col-md-6">
                            <div class="card bg-light h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-calendar-alt"></i> Datos de la Visita
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-home text-success"></i> Residente:
                                        </label>
                                        <div>
                                            {{ $visita->residente ? $visita->residente->nombre_completo : 'Sin asignar' }}
                                            @if($visita->residente && $visita->residente->unidad)
                                                <small class="text-muted">- {{ $visita->residente->unidad }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-clock text-primary"></i> Horario Programado:
                                        </label>
                                        <div>
                                            {{ $visita->fecha_inicio->format('d/m/Y H:i') }} - 
                                            {{ $visita->fecha_fin->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-info-circle text-info"></i> Estado:
                                        </label>
                                        <div>{{ ucfirst(str_replace('_', ' ', $visita->estado)) }}</div>
                                    </div>
                                    
                                    <div class="mb-0">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-calendar-plus text-secondary"></i> Creado:
                                        </label>
                                        <div>{{ $visita->created_at->format('d/m/Y H:i:s') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Registro de Entrada y Salida --}}
                    @if($visita->hora_entrada || $visita->hora_salida)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-history"></i> Historial de Entrada/Salida
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @if($visita->hora_entrada)
                                                <div class="col-md-6">
                                                    <div class="border-end pe-3">
                                                        <h6 class="text-success">
                                                            <i class="fas fa-sign-in-alt"></i> Entrada Registrada
                                                        </h6>
                                                        <p class="mb-1">
                                                            <strong>Fecha/Hora:</strong> 
                                                            {{ $visita->hora_entrada->format('d/m/Y H:i:s') }}
                                                        </p>
                                                        <p class="mb-0">
                                                            <strong>Registrado por:</strong> 
                                                            {{ $visita->userEntrada ? $visita->userEntrada->name : 'Sistema' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($visita->hora_salida)
                                                <div class="col-md-6">
                                                    <div class="ps-3">
                                                        <h6 class="text-primary">
                                                            <i class="fas fa-sign-out-alt"></i> Salida Registrada
                                                        </h6>
                                                        <p class="mb-1">
                                                            <strong>Fecha/Hora:</strong> 
                                                            {{ $visita->hora_salida->format('d/m/Y H:i:s') }}
                                                        </p>
                                                        <p class="mb-0">
                                                            <strong>Registrado por:</strong> 
                                                            {{ $visita->userSalida ? $visita->userSalida->name : 'Sistema' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        @if($visita->observaciones)
                                            <hr>
                                            <div>
                                                <h6 class="text-warning">
                                                    <i class="fas fa-sticky-note"></i> Observaciones:
                                                </h6>
                                                <p class="mb-0">{{ $visita->observaciones }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Acciones según el estado y permisos --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs"></i> Acciones Disponibles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        @if($visita->estado == 'pendiente')
                            @can('operar porteria')
                                <form action="{{ route('visitas.entrada', $visita) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success"
                                            onclick="return confirm('¿Registrar entrada de {{ $visita->nombre_visitante }}?')">
                                        <i class="fas fa-sign-in-alt"></i> Registrar Entrada
                                    </button>
                                </form>
                            @endcan

                            @can('gestionar visitas')
                                <a href="{{ route('visitas.edit', $visita) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar Visita
                                </a>
                            @endcan

                            @can('administrar visitas')
                                <a href="{{ route('visitas.edit', $visita) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar Visita
                                </a>
                            @endcan
                        @endif

                        @if($visita->estado == 'en_curso')
                            @can('operar porteria')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#salidaModal">
                                    <i class="fas fa-sign-out-alt"></i> Registrar Salida
                                </button>
                            @endcan
                        @endif

                        {{-- Botones comunes --}}
                        <a href="{{ route('visitas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Lista
                        </a>

                        @can('operar porteria')
                            <a href="{{ route('visitas.panel-guardia') }}" class="btn btn-outline-info">
                                <i class="fas fa-shield-alt"></i> Panel Guardia
                            </a>
                        @endcan

                        {{-- Botón de imprimir/exportar para admins --}}
                        @can('administrar visitas')
                            <button class="btn btn-outline-dark" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        @endcan
                    </div>

                    {{-- Información contextual según permisos --}}
                    <div class="mt-3">
                        @can('gestionar visitas')
                            @if($visita->estado == 'pendiente' && $visita->residente_id == auth()->user()->residente?->id)
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Tu Visita:</strong> Puedes editar esta visita mientras esté pendiente.
                                    Comparte el código <strong>{{ $visita->codigo }}</strong> con tu visitante.
                                </div>
                            @endif
                        @endcan

                        @can('operar porteria')
                            @if($visita->estado == 'pendiente')
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock"></i>
                                    <strong>Visita Pendiente:</strong> El visitante puede presentarse hasta 30 minutos antes del horario programado.
                                </div>
                            @elseif($visita->estado == 'en_curso')
                                <div class="alert alert-info">
                                    <i class="fas fa-user-clock"></i>
                                    <strong>Visitante Dentro:</strong> Recuerda registrar la salida cuando el visitante se retire.
                                </div>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para registrar salida con observaciones --}}
    @if($visita->estado == 'en_curso')
        @can('operar porteria')
            <div class="modal fade" id="salidaModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-sign-out-alt"></i> Registrar Salida
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('visitas.salida', $visita) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1">
                                                <strong><i class="fas fa-user"></i> Visitante:</strong> 
                                                {{ $visita->nombre_visitante }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1">
                                                <strong><i class="fas fa-id-card"></i> CI:</strong> 
                                                {{ $visita->ci_visitante }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">
                                        <i class="fas fa-sticky-note"></i> Observaciones (Opcional)
                                    </label>
                                    <textarea name="observaciones" 
                                              id="observaciones" 
                                              class="form-control" 
                                              rows="3" 
                                              placeholder="Ej: Salida normal, sin inconvenientes"></textarea>
                                    <small class="text-muted">
                                        Registra cualquier observación sobre la visita o comportamiento del visitante.
                                    </small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-out-alt"></i> Registrar Salida
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    @endif
</div>
@endsection