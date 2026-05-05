@extends('layouts.ap')

@section('content')
<div class="container">
    <h2 class="mb-4">Nuevo Tipo de Cuota</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ups!</strong> Hay errores en el formulario.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tipos-cuotas.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del tipo de cuota</label>
            <input type="text" name="nombre" class="form-control" required value="{{ old('nombre') }}">
        </div>

        <div class="mb-3">
            <label for="frecuencia" class="form-label">Frecuencia</label>
            <select name="frecuencia" class="form-select" required>
                <option value="">-- Selecciona una frecuencia --</option>
                <option value="mensual" {{ old('frecuencia') == 'mensual' ? 'selected' : '' }}>Mensual</option>
                <option value="anual" {{ old('frecuencia') == 'anual' ? 'selected' : '' }}>Anual</option>
                <option value="puntual" {{ old('frecuencia') == 'puntual' ? 'selected' : '' }}>Puntual</option>
            </select>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="editable" value="1" id="editable" {{ old('editable', true) ? 'checked' : '' }}>
            <label class="form-check-label" for="editable">
                ¿Es editable?
            </label>
        </div>

        <button type="submit" class="btn btn-success">Guardar Tipo de Cuota</button>
        <a href="{{ route('tipos-cuotas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
