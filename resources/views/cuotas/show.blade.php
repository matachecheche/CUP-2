@extends('layouts.ap')

@section('content')
<div class="container">
    <h3>Detalle de la Cuota</h3>
    <div class="card mt-4">
        <div class="card-body">
            <p><strong>Título:</strong> {{ $cuota->titulo }}</p>
            <p><strong>Descripción:</strong> {{ $cuota->descripcion }}</p>
            <p><strong>Residente:</strong> {{ $cuota->residente->nombre_completo ?? 'N/A' }}</p>
            <p><strong>Monto:</strong> ${{ number_format($cuota->monto, 2) }}</p>
            <p><strong>Fecha de Emisión:</strong> {{ $cuota->fecha_emision }}</p>
            <p><strong>Fecha de Vencimiento:</strong> {{ $cuota->fecha_vencimiento }}</p>
            <p><strong>Estado:</strong> <span class="badge bg-info">{{ ucfirst($cuota->estado) }}</span></p>
            <p><strong>Observación:</strong> {{ $cuota->observacion ?? '-' }}</p>
            <a href="{{ route('cuotas.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</div>
@endsection
