@extends('layouts.ap')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Multa</h2>

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

    <form action="{{ route('multas.update', $multa->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <!-- Usuario objetivo (Residente o Empleado) como solo lectura -->
        <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" class="form-control" readonly
                value="{{ optional($multa->residente)->nombre_completo
                         ?? optional($multa->empleado)->nombre_completo
                         ?? 'N/A' }}">
        </div>

        <div class="mb-3">
            <label for="motivo" class="form-label">Motivo de la Multa</label>
            <input type="text" name="motivo" id="motivo" class="form-control" required
                   value="{{ old('motivo', $multa->motivo) }}">
        </div>

        <div class="mb-3">
            <label for="monto" class="form-label">Monto (Bs.)</label>
            <input type="number" name="monto" id="monto" step="0.01" class="form-control" required
                   value="{{ old('monto', $multa->monto) }}">
        </div>

        <div class="mb-3">
            <label for="fechaEmision" class="form-label">Fecha de Emisión</label>
            <input type="date" name="fechaEmision" id="fechaEmision" class="form-control" required
                   value="{{ old('fechaEmision', \Carbon\Carbon::parse($multa->fechaEmision)->format('Y-m-d')) }}">
        </div>

        <div class="mb-3">
            <label for="fechaLimite" class="form-label">Fecha Límite</label>
            <input type="date" name="fechaLimite" id="fechaLimite" class="form-control" required
                   value="{{ old('fechaLimite', \Carbon\Carbon::parse($multa->fechaLimite)->format('Y-m-d')) }}">
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-select" required>
                @foreach(['pendiente','pagada','anulada'] as $estado)
                    <option value="{{ $estado }}"
                        {{ old('estado', $multa->estado) === $estado ? 'selected' : '' }}>
                        {{ ucfirst($estado) }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Actualizar Multa</button>
        <a href="{{ route('multas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
