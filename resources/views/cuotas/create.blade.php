@extends('layouts.ap')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const radios = document.querySelectorAll('input[name="destino"]');
        const grupoContainer = document.getElementById('grupoContainer');
        const residenteContainer = document.getElementById('residenteContainer');

        function updateVisibility() {
            const selected = document.querySelector('input[name="destino"]:checked').value;
            grupoContainer.style.display = selected === 'grupo' ? 'block' : 'none';
            residenteContainer.style.display = selected === 'personalizado' ? 'block' : 'none';
        }

        radios.forEach(radio => {
            radio.addEventListener('change', updateVisibility);
        });

        updateVisibility(); // por si viene con old('destino')
    });
</script>

@section('content')
<div class="container">
    <h2 class="mb-4">Emitir Nueva Cuota</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>¡Ups!</strong> Hay algunos problemas con tu entrada.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('cuotas.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="{{ old('titulo') }}" required>
        </div>


        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion') }}" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="fecha_emision" class="form-label">Fecha de Emisión</label>
                <input type="date" name="fecha_emision" class="form-control" value="{{ old('fecha_emision') }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                <input type="date" name="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento') }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="monto" class="form-label">Monto</label>
            <input type="number" step="0.01" name="monto" class="form-control" value="{{ old('monto') }}" required>
        </div>

        <div class="mb-3">
            <label for="tipo_cuota_id" class="form-label">Tipo de Cuota</label>
            <select name="tipo_cuota_id" class="form-select" required>
                <option value="">-- Selecciona un tipo de cuota --</option>
                @foreach ($tiposCuotas as $tipo)
                <option value="{{ $tipo->id }}" {{ old('tipo_cuota_id') == $tipo->id ? 'selected' : '' }}>
                    {{ $tipo->nombre }} ({{ ucfirst($tipo->frecuencia) }})
                </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="estado" class="form-label">Estado de la cuota</label>
            <select name="estado" class="form-select">
                <option value="pendiente" selected>Pendiente</option>
                <option value="activa">Activa</option>
                <option value="cancelada">Cancelada</option>
            </select>
        </div>


        <div class="mb-3">
            <label class="form-label">Emitir cuota a:</label>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="destino" value="todos" id="opcionTodos" checked>
                <label class="form-check-label" for="opcionTodos">Todos los residentes</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="destino" value="grupo" id="opcionGrupo">
                <label class="form-check-label" for="opcionGrupo">Grupo (rol: propietarios, inquilinos, etc.)</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="destino" value="personalizado" id="opcionPersonalizado">
                <label class="form-check-label" for="opcionPersonalizado">Seleccionar residente específico</label>
            </div>
        </div>

        {{-- SELECT de grupo (rol) --}}
        <div class="mb-3" id="grupoContainer" style="display: none;">
            <label for="grupo_rol" class="form-label">Selecciona el grupo</label>
            <select name="grupo_rol" id="grupo_rol" class="form-select">
                <option value="">-- Selecciona un grupo --</option>
                @foreach ($roles as $rol)
                <option value="{{ $rol }}">{{ ucfirst($rol) }}</option>
                @endforeach
            </select>
        </div>

        {{-- SELECT de residente individual --}}
        <div class="mb-3" id="residenteContainer" style="display: none;">
            <label for="residente_id" class="form-label">Selecciona un residente</label>
            <select name="residente_id" id="residente_id" class="form-select">
                <option value="">-- Selecciona un residente --</option>
                @foreach ($residentes as $residente)
                <option value="{{ $residente->id }}">
                    {{ $residente->nombre }} {{ $residente->apellido }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
    <label for="observacion" class="form-label">Observación (opcional)</label>
    <textarea name="observacion" class="form-control" rows="2">{{ old('observacion') }}</textarea>
</div>

        <button type="submit" class="btn btn-success">Guardar Cuota</button>
        <a href="{{ route('cuotas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection