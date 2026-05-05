@extends('layouts.ap')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Tipo de Cuota</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ups!</strong> Hay errores con tu entrada:<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tipos-cuotas.update', $tipo->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del tipo de cuota</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $tipo->nombre) }}" required>
        </div>

        <div class="mb-3">
            <label for="frecuencia" class="form-label">Frecuencia</label>
            <select name="frecuencia" class="form-select" required>
                <option value="mensual" {{ $tipo->frecuencia == 'mensual' ? 'selected' : '' }}>Mensual</option>
                <option value="anual" {{ $tipo->frecuencia == 'anual' ? 'selected' : '' }}>Anual</option>
                <option value="puntual" {{ $tipo->frecuencia == 'puntual' ? 'selected' : '' }}>Puntual</option>
            </select>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="editable" value="1" id="editable" {{ $tipo->editable ? 'checked' : '' }}>
            <label class="form-check-label" for="editable">
                ¿Editable por el administrador?
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('tipos-cuotas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
