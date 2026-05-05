@extends('layouts.ap')

@section('content')
<div class="container">
    <h2 class="mb-4">Nueva Multa</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ups!</strong> Hay errores en el formulario.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('multas.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="motivo" class="form-label">Motivo de la Multa</label>
            <input type="text" name="motivo" id="motivo" class="form-control" required
                   value="{{ old('motivo') }}">
        </div>

        <div class="mb-3">
            <label for="monto" class="form-label">Monto (Bs.)</label>
            <input type="number" name="monto" id="monto" step="0.01" class="form-control" required
                   value="{{ old('monto') }}">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="fechaEmision" class="form-label">Fecha de Emisión</label>
                <input type="date" name="fechaEmision" id="fechaEmision" class="form-control" required
                    value="{{ old('fechaEmision', now()->format('Y-m-d')) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="fechaLimite" class="form-label">Fecha Límite</label>
                <input type="date" name="fechaLimite" id="fechaLimite" class="form-control" required
                    value="{{ old('fechaLimite', now()->addDays(7)->format('Y-m-d')) }}">
            </div>
        </div>

        <div class="mb-3">
            <label for="residente_id" class="form-label">Residente (opcional)</label>
            <select name="residente_id" id="residente_id" class="form-select">
                <option value="">-- Selecciona un Residente --</option>
                @foreach($residentes as $res)
                    <option value="{{ $res->id }}"
                        {{ old('residente_id') == $res->id ? 'selected' : '' }}>
                        {{ $res->nombre_completo }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="empleado_id" class="form-label">Empleado (opcional)</label>
            <select name="empleado_id" id="empleado_id" class="form-select">
                <option value="">-- Selecciona un Empleado --</option>
                @foreach($empleados as $emp)
                    <option value="{{ $emp->id }}"
                        {{ old('empleado_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->nombre_completo }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">
                Debes elegir <strong>solo uno</strong>: Residente o Empleado.
            </div>
        </div>

        <button type="submit" class="btn btn-success">Guardar Multa</button>
        <a href="{{ route('multas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
