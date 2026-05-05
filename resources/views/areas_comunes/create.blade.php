@extends('layouts.ap')

@section('content')
<div class="container">
    <h2 class="mb-4">Nueva Área Común</h2>

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

    <form action="{{ route('areas-comunes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Área Común</label>
            <input type="text" name="nombre" class="form-control" required value="{{ old('nombre') }}">
        </div>

        <div class="mb-3">
            <label for="monto" class="form-label">Monto (Bs.)</label>
            <input type="number" step="0.01" name="monto" class="form-control" required value="{{ old('monto') }}">
        </div>

        <button type="submit" class="btn btn-success">Guardar Área Común</button>
        <a href="{{ route('areas-comunes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
