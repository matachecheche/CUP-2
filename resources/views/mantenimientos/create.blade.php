@extends('layouts.ap')
@section('content')
<div class="container">
    <h2 class="mb-4">Registrar Nuevo Mantenimiento</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('mantenimientos.store') }}">
        @csrf

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Estado</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="estado" value="1" {{ old('estado', '1') == '1' ? 'checked' : '' }}>
                <label class="form-check-label">Activo</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="estado" value="0" {{ old('estado') == '0' ? 'checked' : '' }}>
                <label class="form-check-label">Inactivo</label>
            </div>
        </div>

        <div class="mb-3">
            <label for="fecha_hora" class="form-label">Fecha y Hora</label>
            <input type="datetime-local" name="fecha_hora" class="form-control" value="{{ old('fecha_hora') }}" required>
        </div>

        <div class="mb-3">
            <label for="monto" class="form-label">Monto</label>
            <input type="number" step="0.01" name="monto" class="form-control" value="{{ old('monto') }}" required>
        </div>

        <div class="mb-3">
            <label for="usuario_id" class="form-label">Usuario</label>
            <select name="usuario_id" class="form-select" required>
                <option value="">-- Seleccionar Usuario --</option>
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id }}" {{ old('usuario_id') == $usuario->id ? 'selected' : '' }}>
                        {{ $usuario->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="empresa_id" class="form-label">Empresa (opcional)</label>
            <select name="empresaExterna_id" class="form-select">
                <option value="">-- Ninguna --</option>
                 @foreach($empresas  as $empresa)
                    <option value="{{ $empresa->id }}" {{ old('empresaExterna_id') == $empresa->id ? 'selected' : '' }}>
                        {{ $empresa->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Registrar</button>
        <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection