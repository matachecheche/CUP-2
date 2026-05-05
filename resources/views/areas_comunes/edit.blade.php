@extends('layouts.ap')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Área Común</h2>

    {{-- Mensajes de error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ups!</strong> Hay errores en el formulario:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('areas-comunes.update', $areaComun->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Área Común</label>
            <input type="text"
                   name="nombre"
                   class="form-control"
                   required
                   value="{{ old('nombre', $areaComun->nombre) }}">
        </div>

        <div class="mb-3">
            <label for="monto" class="form-label">Monto (Bs.)</label>
            <input type="number"
                   step="0.01"
                   name="monto"
                   class="form-control"
                   required
                   value="{{ old('monto', $areaComun->monto) }}">
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" class="form-select" required>
                <option value="activo"        {{ old('estado', $areaComun->estado) == 'activo'        ? 'selected' : '' }}>Activo</option>
                <option value="inactivo"      {{ old('estado', $areaComun->estado) == 'inactivo'      ? 'selected' : '' }}>Inactivo</option>
                <option value="mantenimiento"{{ old('estado', $areaComun->estado) == 'mantenimiento'? 'selected' : '' }}>Mantenimiento</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Actualizar Área Común</button>
        <a href="{{ route('areas-comunes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
