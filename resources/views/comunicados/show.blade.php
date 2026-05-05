@extends('layouts.ap')

@section('content')
<div class="container">
    <h2>{{ $comunicado->titulo }}</h2>
    <p><strong>Tipo:</strong> {{ $comunicado->tipo }}</p>
    <p><strong>Fecha:</strong> {{ $comunicado->fecha_publicacion }}</p>
    <p><strong>Contenido:</strong></p>
    <p>{{ $comunicado->contenido }}</p>

    <a href="{{ route('comunicados.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
