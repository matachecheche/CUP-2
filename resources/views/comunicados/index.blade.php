@extends('layouts.ap')

@section('content')
<div class="container">
    <h1 class="mb-4">Lista de Comunicados</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(auth()->check() && !auth()->user()->residente_id && !auth()->user()->empleado_id)
    <a href="{{ route('comunicados.create') }}" class="btn btn-primary mb-3">Nuevo Comunicado</a>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Título</th>
                <th>Tipo</th>
                <th>Contenido</th>
                <th>Autor</th>
                <th>Fecha de Publicación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($comunicados as $comunicado)
                <tr>
                    <td>{{ $comunicado->titulo }}</td>
                    <td>{{ $comunicado->tipo }}</td>
                    <td>{{ Str::limit($comunicado->contenido, 50) }}</td>
                    <td>{{ $comunicado->usuario->name ?? '---' }}</td>
                    <td>{{ $comunicado->fecha_publicacion ? $comunicado->fecha_publicacion->format('d/m/Y H:i') : 'Inmediato' }}</td>
                    <td>
                        @if(auth()->check() && !auth()->user()->residente_id && !auth()->user()->empleado_id)
                        <a href="{{ route('comunicados.edit', $comunicado->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('comunicados.destroy', $comunicado->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Eliminar este comunicado?')">Eliminar</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $comunicados->links() }}
    </div>
</div>
@endsection
