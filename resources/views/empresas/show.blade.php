{{-- resources/views/empresas/show.blade.php --}}
@extends('layouts.ap')

@section('content')
<div class="container">
    <h2 class="mb-4">Detalle de la Empresa</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $empresa->nombre }}</p>
            <p><strong>Servicio:</strong> {{ $empresa->servicio }}</p>
            <p><strong>Teléfono:</strong> {{ $empresa->telefono }}</p>
            <p><strong>Correo:</strong> {{ $empresa->correo }}</p>
            <p><strong>Dirección:</strong> {{ $empresa->direccion }}</p>
            <p><strong>Observación:</strong> {{ $empresa->observacion }}</p>
        </div>
    </div>

    <a href="{{ route('empresas.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection