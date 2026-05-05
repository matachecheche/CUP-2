<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pago</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        .container { max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px; }
        .titulo { text-align: center; font-size: 20px; margin-bottom: 20px; }
        .linea { margin-bottom: 10px; }
        .btn-imprimir { margin-top: 20px; text-align: center; }
        .btn-imprimir button { padding: 8px 16px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="titulo">Comprobante de Pago</div>

        <div class="linea"><strong>ID de Pago:</strong> {{ $pago->id }}</div>
        <div class="linea"><strong>Monto:</strong> Bs {{ number_format($pago->monto_pagado, 2) }}</div>
        <div class="linea"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}</div>
        <div class="linea"><strong>Método:</strong> {{ ucfirst($pago->metodo) }}</div>
        <div class="linea"><strong>Estado Pago:</strong> {{ ucfirst($pago->estado) }}</div>

        @if($pago->cuota)
            <div class="linea"><strong>Concepto:</strong> Cuota – {{ $pago->cuota->titulo ?? '-' }}</div>
            <div class="linea"><strong>Residente:</strong> {{ optional($pago->cuota->residente)->nombre_completo }}</div>
        @elseif($pago->multa)
            <div class="linea"><strong>Concepto:</strong> Multa – {{ $pago->multa->motivo }}</div>
            <div class="linea">
                <strong>Usuario:</strong>
                {{ optional($pago->multa->residente)->nombre_completo
                   ?? optional($pago->multa->empleado)->nombre_completo }}
            </div>
        @endif

        @if($pago->observacion)
            <div class="linea"><strong>Observación:</strong> {{ $pago->observacion }}</div>
        @endif

        {{--@if($pago->comprobante)
            <div class="linea">
                <strong>Comprobante:</strong><br>
                <img src="{{ Storage::url($pago->comprobante) }}" style="max-width:100%; margin-top:10px;">
            </div>
        @endif--}}

        <div class="btn-imprimir">
            <button onclick="window.print()">Imprimir / Guardar como PDF</button>
        </div>
    </div>
</body>
</html>
