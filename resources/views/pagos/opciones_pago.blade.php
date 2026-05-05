@extends('layouts.ap')

@section('title', 'Pagar cuota')

@section('content')
<div class="container mt-4">
    <h4>
        Pago de Cuota/Multa:
        {{ $entidad->concepto }} (Bs {{ $entidad->monto }})
    </h4>

    <div class="row">
        <div class="col-md-6">
            <h5>Pago por QR</h5>
            <p>Escanee este código y suba su comprobante.</p>

            {{-- Mostrar QR generado dinámicamente --}}
            <img src="{{ $qrBase64 }}" width="200" alt="QR de pago">

            <form method="POST" enctype="multipart/form-data"
                @if($tipo === 'cuota')
                    action="{{ route('pagos.qr') }}"
                @elseif($tipo === 'multa')
                    action="{{ route('pagos.qr.multa') }}"
                @endif
                >
                @csrf

                @if($tipo === 'cuota')
                    <input type="hidden" name="cuota_id"  value="{{ $entidad->id }}">
                @else
                    <input type="hidden" name="multa_id"  value="{{ $entidad->id }}">
                @endif

                <input type="file" name="comprobante" class="form-control mb-2" required>
                <button type="submit" class="btn btn-primary btn-sm">Enviar comprobante</button>
            </form>
        </div>

        <!-- Pago con Stripe -->
        <div class="col-md-6">
            <h5>Pago con tarjeta (Stripe)</h5>
            <p>Haz clic para pagar de forma automática.</p>

            @if($tipo === 'cuota')
                <form method="POST" action="{{ route('pagos.stripe') }}">
            @else
                <form method="POST" action="{{ route('pagos.stripe.multa') }}">
            @endif
                @csrf
                <input type="hidden" name="{{ $tipo }}_id" value="{{ $entidad->id }}">
                <button type="submit" class="btn btn-success btn-sm">
                Pagar {{ ucfirst($tipo) }} con Stripe
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
