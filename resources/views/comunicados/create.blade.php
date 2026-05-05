@extends('layouts.ap')

@section('content')
<div class="container">
    <h2 class="mb-4">Nuevo Comunicado</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('comunicados.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" required value="{{ old('titulo') }}">
        </div>

        <div class="mb-3">
            <label for="contenido" class="form-label">Contenido</label>
            <textarea name="contenido" class="form-control" rows="4" required>{{ old('contenido') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select name="tipo" class="form-select" required>
                <option value="">Seleccione</option>
                <option value="Informativo" {{ old('tipo') == 'Informativo' ? 'selected' : '' }}>Informativo</option>
                <option value="Urgente" {{ old('tipo') == 'Urgente' ? 'selected' : '' }}>Urgente</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_publicacion" class="form-label">Fecha y Hora de Publicación</label>
            <input type="datetime-local" name="fecha_publicacion" class="form-control" value="{{ old('fecha_publicacion') }}">
            <small class="text-muted">Si se deja vacío, se publicará inmediatamente.</small>
        </div>

        <button type="submit" class="btn btn-primary">Publicar Comunicado</button>
        <a href="{{ route('comunicados.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
