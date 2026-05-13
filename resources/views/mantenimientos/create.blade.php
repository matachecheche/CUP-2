@extends('plantilla')

@section('title', 'Crear Mantenimiento')

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
    </style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Crear Mantenimiento</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('mantenimientos.index') }}">Mantenimientos</a></li>
        <li class="breadcrumb-item active">Crear</li>
    </ol>

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Nuevo Registro de Mantenimiento</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('mantenimientos.store') }}" method="POST" id="formMantenimiento">
                @csrf

                <div class="row">
                    {{-- Descripción --}}
                    <div class="col-md-12 mb-3">
                        <label for="descripcion" class="form-label fw-bold">Descripción del Mantenimiento <span class="text-danger">*</span></label>
                        <textarea 
                            id="descripcion" 
                            name="descripcion" 
                            class="form-control @error('descripcion') is-invalid @enderror"
                            rows="3"
                            placeholder="Describe el tipo de mantenimiento (mínimo 10 caracteres)"
                            required>{{ old('descripcion') }}</textarea>
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
                            <option value="">-- Seleccionar Estado --</option>
                            <option value="1" {{ old('estado') == 1 ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('estado') == 0 ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- NUEVO: Prioridad --}}
                    <div class="col-md-6 mb-3">
                        <label for="prioridad" class="form-label fw-bold">Prioridad <span class="text-danger">*</span></label>
                        <select id="prioridad" name="prioridad" class="form-control @error('prioridad') is-invalid @enderror" required onchange="actualizarBadgePrioridad()">
                            <option value="">-- Seleccionar Prioridad --</option>
                            @foreach($prioridades as $valor => $etiqueta)
                                <option value="{{ $valor }}" {{ old('prioridad') == $valor ? 'selected' : '' }}>
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
                            value="{{ old('fecha_hora', now()->format('Y-m-d\TH:i')) }}"
                            required>
                        <small class="form-text text-muted">Debe ser hoy o posterior</small>
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
                            placeholder="0.00"
                            value="{{ old('monto') }}"
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
                            <option value="">-- Seleccionar Usuario --</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" {{ old('usuario_id') == $usuario->id ? 'selected' : '' }}>
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
                                <option value="{{ $empresa->id }}" {{ old('empresaExterna_id') == $empresa->id ? 'selected' : '' }}>
                                    {{ $empresa->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="row mt-4">
                    <div class="col-md-12 d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Guardar Mantenimiento
                        </button>
                        <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
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

    document.getElementById('formMantenimiento').addEventListener('submit', function(e) {
        const prioridad = document.getElementById('prioridad').value;
        if (!prioridad) {
            e.preventDefault();
            alert('Por favor selecciona un nivel de prioridad');
            return false;
        }
    });
</script>
@endsection
@endsection