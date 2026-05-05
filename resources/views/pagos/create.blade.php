@extends('layouts.ap')
{{-- JavaScript para rellenar automáticamente el monto --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cuotaSelect = document.getElementById('cuotaSelect');
        const montoInput = document.getElementById('montoPagado');

        cuotaSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const monto = selectedOption.getAttribute('data-monto');
            if (monto) {
                montoInput.value = parseFloat(monto).toFixed(2);
            } else {
                montoInput.value = '';
            }
        });
    });
</script>

@section('content')
<div class="container">
    <h2 class="mb-4">Registrar Pago</h2>

    @if($errors->any())
    <div class="alert alert-danger">
        <strong>¡Ups!</strong> Hay algunos errores:<br><br>
        <ul>
            @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('pagos.store') }}" method="POST">
        @csrf

        {{-- Selección de cuota (solo si no es residente o si tiene varias cuotas) --}}
        <div class="mb-3">
            <label for="cuota_id" class="form-label">Cuota asociada</label>
            <select name="cuota_id" id="cuotaSelect" class="form-select" required>
                <option value="">-- Selecciona una cuota --</option>
                @foreach($cuotas as $cuota)
                <option value="{{ $cuota->id }}" data-monto="{{ $cuota->monto }}">
                    Cuota #{{ $cuota->id }} - {{ $cuota->residente->nombre_completo ?? 'Sin residente' }} (Bs {{ number_format($cuota->monto, 2) }})
                </option>
                @endforeach
            </select>
        </div>

        {{-- Monto pagado (prellenado según la cuota) --}}
        <div class="mb-3">
            <label class="form-label">Monto Pagado</label>
            <input type="number" step="0.01" name="monto_pagado" id="montoPagado" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha de Pago</label>
            <input type="text" class="form-control" value="{{ now()->toDateString() }}" disabled>
            <input type="hidden" name="fecha_pago" value="{{ now()->toDateString() }}">
        </div>


        {{-- Método de pago --}}
        <div class="mb-3">
            <label class="form-label">Método de Pago</label>
            <select name="metodo" class="form-select" required>
                <option value="">-- Selecciona --</option>
                <option value="efectivo">Efectivo</option>
                <option value="transferencia">Transferencia</option>
                <option value="qr">QR</option>
                {{-- Agrega aquí otros métodos como "Stripe" si planeas integrar pasarela --}}
            </select>
        </div>

        {{-- Observación opcional --}}
        <div class="mb-3">
            <label class="form-label">Observación (opcional)</label>
            <textarea name="observacion" class="form-control" rows="2"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Registrar Pago</button>
        <a href="{{ route('pagos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Al cambiar de cuota, actualiza el monto automáticamente
    document.getElementById('cuotaSelect')?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const monto = selectedOption.getAttribute('data-monto');
        document.getElementById('montoPagado').value = monto || '';
    });
</script>
@endsection