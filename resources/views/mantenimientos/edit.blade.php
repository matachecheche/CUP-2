@extends('plantilla')

@section('title', 'Editar Mantenimiento')

@push('css')
    <style>
        .priority-badge {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
            margin-top: 0.5rem;
        }
        .priority-alta { background-color: #fee2e2; color: #991b1b; }
        .priority-media { background-color: #fef3c7; color: #92400e; }
        .priority-baja { background-color: #dbeafe; color: #1e40af; }
        .alert-info-custom {
            background-color: #e0f2fe;
            border-left: 4px solid #0284c7;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Mantenimiento #{{ $mantenimiento->id }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('mantenimientos.index') }}">Mantenimientos</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">¡Error en la validación!</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="alert-info-custom">
        <strong><i class="fas fa-info-circle"></i> Información actual:</strong><br>
        Estado: <strong>{{ $mantenimiento->estado ? 'Activo' : 'Inactivo' }}</strong> | 
        Prioridad: <span class="priority-badge priority-{{ $mantenimiento->prioridad }}">{{ ucfirst($mantenimiento->prioridad) }}</span> | 
        Creado: {{ $mantenimiento->created_at->format('d/m/Y H:i') }}
    </div>

    <div class="card shadow-lg">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit"></i> Actualizar Registro de Mantenimiento</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('mantenimientos.update', $mantenimiento->id) }}" method="POST" id="formMantenimiento">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Descripción --}}
                    <div class="col-md-12 mb-3">
                        <label for="descripcion" class="form-label fw-bold">Descripción del Mantenimiento <span class="text-danger">*</span></label>
                        <textarea 
                            id="descripcion" 
                            name="descripcion" 
                            class="form-control @error('descripcion') is-invalid @enderror"
                            rows="3"
                            required>{{ old('descripcion', $mantenimiento->descripcion) }}</textarea>
                        <small class="form-text text-muted">Mínimo 10 caracteres, máximo 500</small>
                        @error('descripcion')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Estado --}}
                    <div class="col-md-6 mb-3">
                        <label for="estado" class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                        <select id="estado" name="estado" class="form-control @error('estado') is-invalid @enderror" required>
                            <option value="1" {{ old('estado', $mantenimiento->estado) == 1 ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('estado', $mantenimiento->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Prioridad --}}
                    <div class="col-md-6 mb-3">
                        <label for="prioridad" class="form-label fw-bold">Prioridad <span class="text-danger">*</span></label>
                        <select id="prioridad" name="prioridad" class="form-control @error('prioridad') is-invalid @enderror" required onchange="actualizarBadgePrioridad()">
                            @foreach($prioridades as $valor => $etiqueta)
                                <option value="{{ $valor }}" {{ old('prioridad', $mantenimiento->prioridad) == $valor ? 'selected' : '' }}>
                                    {{ $etiqueta }}
                                </option>
                            @endforeach
                        </select>
                        <div id="badgePrioridad"></div>
                        @error('prioridad')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Fecha y Hora --}}
                    <div class="col-md-6 mb-3">
                        <label for="fecha_hora" class="form-label fw-bold">Fecha y Hora <span class="text-danger">*</span></label>
                        <input 
                            type="datetime-local" 
                            id="fecha_hora" 
                            name="fecha_hora" 
                            class="form-control @error('fecha_hora') is-invalid @enderror"
                            value="{{ old('fecha_hora', $mantenimiento->fecha_hora) }}"
                            required>
                        @error('fecha_hora')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Monto --}}
                    <div class="col-md-6 mb-3">
                        <label for="monto" class="form-label fw-bold">Monto (COP) <span class="text-danger">*</span></label>
                        <input 
                            type="number" 
                            id="monto" 
                            name="monto" 
                            class="form-control @error('monto') is-invalid @enderror"
                            step="0.01" 
                            min="0" 
                            value="{{ old('monto', $mantenimiento->monto) }}"
                            required>
                        @error('monto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Usuario Responsable --}}
                    <div class="col-md-6 mb-3">
                        <label for="usuario_id" class="form-label fw-bold">Usuario Responsable <span class="text-danger">*</span></label>
                        <select id="usuario_id" name="usuario_id" class="form-control @error('usuario_id') is-invalid @enderror" required>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" {{ old('usuario_id', $mantenimiento->usuario_id) == $usuario->id ? 'selected' : '' }}>
                                    {{ $usuario->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('usuario_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Empresa Externa --}}
                    <div class="col-md-6 mb-3">
                        <label for="empresaExterna_id" class="form-label fw-bold">Empresa Externa (Opcional)</label>
                        <select id="empresaExterna_id" name="empresaExterna_id" class="form-control">
                            <option value="">-- Sin empresa asignada --</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id }}" {{ old('empresaExterna_id', $mantenimiento->empresaExterna_id) == $empresa->id ? 'selected' : '' }}>
                                    {{ $empresa->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="row mt-4">
                    <div class="col-md-12 d-flex gap-2">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-check-circle"></i> Actualizar Mantenimiento
                        </button>
                        <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <form action="{{ route('mantenimientos.destroy', $mantenimiento->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('¿Estás seguro?')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function actualizarBadgePrioridad() {
        const select = document.getElementById('prioridad');
        const badge = document.getElementById('badgePrioridad');
        const valor = select.value;

        badge.innerHTML = '';
        if (valor) {
            const clases = {
                'alta': 'priority-alta',
                'media': 'priority-media',
                'baja': 'priority-baja'
            };
            const etiquetas = {
                'alta': 'ALTA PRIORIDAD ⚠️',
                'media': 'Prioridad Media ⏳',
                'baja': 'Baja Prioridad ✓'
            };
            badge.innerHTML = `<span class="priority-badge ${clases[valor]}">${etiquetas[valor]}</span>`;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        actualizarBadgePrioridad();
    });
</script>
@endsection
@endsection