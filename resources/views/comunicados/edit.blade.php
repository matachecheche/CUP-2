@extends('layouts.ap')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Comunicado</h2>

    <form method="POST" action="{{ route('comunicados.update', $comunicado->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" class="form-control" name="titulo" value="{{ old('titulo', $comunicado->titulo) }}" required>
        </div>

        <div class="mb-3">
            <label for="contenido" class="form-label">Contenido</label>
            <textarea class="form-control" name="contenido" rows="5" required>{{ old('contenido', $comunicado->contenido) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select class="form-select" name="tipo" required>
                <option value="Informativo" {{ $comunicado->tipo === 'Informativo' ? 'selected' : '' }}>Informativo</option>
                <option value="Urgente" {{ $comunicado->tipo === 'Urgente' ? 'selected' : '' }}>Urgente</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_publicacion" class="form-label">Fecha y Hora de Publicación</label>
            <input type="datetime-local" name="fecha_publicacion" class="form-control"
                value="{{ old('fecha_publicacion', $comunicado->fecha_publicacion ? $comunicado->fecha_publicacion->format('Y-m-d\TH:i') : '') }}">
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection
