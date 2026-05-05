@extends('layouts.ap')

@section('content')
<div class="container">
    <h3 class="mb-4">Editar Cuota</h3>

    <form action="{{ route('cuotas.update', $cuota->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="{{ old('titulo', $cuota->titulo) }}" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion', $cuota->descripcion) }}" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="fecha_emision" class="form-label">Fecha de Emisión</label>
                <input type="date" name="fecha_emision" class="form-control" value="{{ old('fecha_emision', $cuota->fecha_emision) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                <input type="date" name="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento', $cuota->fecha_vencimiento) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="monto" class="form-label">Monto</label>
            <input type="number" name="monto" class="form-control" step="0.01" value="{{ old('monto', $cuota->monto) }}" required>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="pendiente" {{ $cuota->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="activa" {{ $cuota->estado == 'activa' ? 'selected' : '' }}>Activa</option>
                <option value="cancelada" {{ $cuota->estado == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                <option value="pagado" {{ $cuota->estado == 'pagado' ? 'selected' : '' }}>Pagado</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="observacion" class="form-label">Observación</label>
            <textarea name="observacion" class="form-control">{{ old('observacion', $cuota->observacion) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('cuotas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
