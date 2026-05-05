@extends('layouts.ap')

@section('content')
    <div class="container">
        <h4>Pago de multa</h4>
        <p>Monto: Bs {{ $multa->monto }}</p>
        <p>Motivo: {{ $multa->motivo }}</p>

        <img src="{{ $qrBase64 }}" alt="QR de pago">

        {{-- Formulario de pago por QR --}}
        <form action="{{ route('pagos-multa.qr') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="multa_id" value="{{ $multa->id }}">
            <label>Subir comprobante:</label>
            <input type="file" name="comprobante" required class="form-control mb-2">
            <button type="submit" class="btn btn-primary">Enviar comprobante</button>
        </form>

        {{-- Botón Stripe --}}
        <form action="{{ route('pagos-multa.stripe') }}" method="POST">
            @csrf
            <input type="hidden" name="multa_id" value="{{ $multa->id }}">
            <button type="submit" class="btn btn-success mt-3">Pagar con Stripe</button>
        </form>
    </div>
@endsection
