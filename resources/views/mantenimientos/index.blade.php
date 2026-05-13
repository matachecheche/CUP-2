@extends('plantilla')

@section('title', 'Mantenimientos')

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .priority-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
        }
        .priority-alta {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .priority-media {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }
        .priority-baja {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }
    </style>
@endpush

@section('content')

@php
    $prioridades = $prioridades ?? \App\Models\Mantenimiento::prioridades();
@endphp

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            timer: 3000,
            timerProgressBar: true
        });
    </script>
@endif

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Gestión de Mantenimientos</h1>
        <a href="{{ route('mantenimientos.create') }}" class="btn btn-success btn-lg">
            <i class="fas fa-plus-circle"></i> Nuevo Mantenimiento
        </a>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Mantenimientos</li>
    </ol>

    {{-- FILTROS --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-filter"></i> Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('mantenimientos.index') }}" method="GET" class="row g-3">
                {{-- Búsqueda General --}}
                <div class="col-md-4">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Buscar por descripción..." 
                        class="form-control"
                        value="{{ request('search') }}">
                </div>

                {{-- Filtro por Prioridad --}}
                <div class="col-md-3">
                    <select name="prioridad" class="form-control">
                        <option value="">Todas las prioridades</option>
                        @foreach($prioridades as $valor => $etiqueta)
                            <option value="{{ $valor }}" {{ request('prioridad') == $valor ? 'selected' : '' }}>
                                {{ $etiqueta }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por Estado --}}
                <div class="col-md-3">
                    <select name="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ request('estado') == 1 ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('estado') == 0 ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>

                {{-- Botón Buscar --}}
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA DE MANTENIMIENTOS --}}
    <div class="card shadow-lg">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>
                            <a href="{{ route('mantenimientos.index', array_merge(request()->query(), ['sort' => 'descripcion', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                                Descripción
                                @if(request('sort') === 'descripcion')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('mantenimientos.index', array_merge(request()->query(), ['sort' => 'prioridad', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                                Prioridad
                                @if(request('sort') === 'prioridad')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Estado</th>
                        <th>
                            <a href="{{ route('mantenimientos.index', array_merge(request()->query(), ['sort' => 'fecha_hora', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                                Fecha
                                @if(request('sort') === 'fecha_hora')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('mantenimientos.index', array_merge(request()->query(), ['sort' => 'monto', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}">
                                Monto
                                @if(request('sort') === 'monto')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Usuario</th>
                        <th>Empresa</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mantenimientos as $mantenimiento)
                        <tr>
                            <td><strong>#{{ $mantenimiento->id }}</strong></td>
                            <td>{{ Str::limit($mantenimiento->descripcion, 40) }}</td>
                            <td>
                                <span class="priority-badge priority-{{ $mantenimiento->prioridad }}">
                                    {{ ucfirst($mantenimiento->prioridad) }}
                                </span>
                            </td>
                            <td>
                                @if($mantenimiento->estado)
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Activo</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-times"></i> Inactivo</span>
                                @endif
                            </td>
                            <td>{{ $mantenimiento->fecha_hora->format('d/m/Y H:i') }}</td>
                            <td><strong>${{ number_format($mantenimiento->monto, 2) }}</strong></td>
                            <td>{{ $mantenimiento->usuario->name ?? 'N/A' }}</td>
                            <td>{{ $mantenimiento->empresa->nombre ?? 'Sin asignar' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('mantenimientos.edit', $mantenimiento->id) }}" class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('mantenimientos.destroy', $mantenimiento->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Confirmar eliminación?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-inbox" style="font-size: 2rem; color: #ccc;"></i>
                                <p class="mt-2 text-muted">No hay mantenimientos registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINACIÓN --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $mantenimientos->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection