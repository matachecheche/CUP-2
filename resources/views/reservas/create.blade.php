@extends('layouts.ap')

@section('content')
<div class="container">
    <h2 class="mb-4">Agendar Reserva</h2>

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

    <form action="{{ route('reservas.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="area_comun_id" class="form-label">Área Común</label>
            <select name="area_comun_id" id="area_comun_id" class="form-select" required>
                <option value="">-- Selecciona un área común --</option>
                @foreach ($areasComunes as $area)
                    <option value="{{ $area->id }}" data-monto="{{ $area->monto }}" {{ old('area_comun_id') == $area->id ? 'selected' : '' }}>
                        {{ $area->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" name="fecha" id="fecha" class="form-control" value="{{ old('fecha') }}" required
                min="{{ date('Y-m-d') }}">
        </div>

        <div class="mb-3">
            <label for="hora_inicio" class="form-label">Hora Inicio</label>
            <select name="hora_inicio" id="hora_inicio" class="form-select" required>
                <option value="">-- Selecciona hora inicio --</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="hora_fin" class="form-label">Hora Fin</label>
            <select name="hora_fin" id="hora_fin" class="form-select" required>
                <option value="">-- Selecciona hora fin --</option>
            </select>
        </div>

        <!-- Cuadro para mostrar monto total -->
        <div class="mb-3">
            <label class="form-label">Monto Total (Bs.)</label>
            <input type="text" id="monto_total" class="form-control" readonly value="0.00">
        </div>

        <div class="mb-3">
            <label for="observacion" class="form-label">Observación</label>
            <textarea name="observacion" id="observacion" class="form-control">{{ old('observacion') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Guardar Reserva</button>
        <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const areaSelect = document.getElementById('area_comun_id');
  const fechaInput = document.getElementById('fecha');
  const horaInicioSelect = document.getElementById('hora_inicio');
  const horaFinSelect = document.getElementById('hora_fin');
  const montoTotalInput = document.getElementById('monto_total');

  // Función para cargar horas disponibles (fetch desde API)
  async function cargarHorasDisponibles() {
    const areaId = areaSelect.value;
    const fecha = fechaInput.value;
    if (!areaId || !fecha) {
      horaInicioSelect.innerHTML = '<option value="">-- Selecciona hora inicio --</option>';
      horaFinSelect.innerHTML = '<option value="">-- Selecciona hora fin --</option>';
      montoTotalInput.value = '0.00';
      return;
    }
    try {
      const response = await fetch(`/api/horas-libres?area_comun_id=${areaId}&fecha=${fecha}`);
      if (!response.ok) throw new Error('Error al cargar horas');
      const horas = await response.json();

      horaInicioSelect.innerHTML = '<option value="">-- Selecciona hora inicio --</option>';
      horaFinSelect.innerHTML = '<option value="">-- Selecciona hora fin --</option>';

      horas.forEach(hora => {
        const optionInicio = document.createElement('option');
        optionInicio.value = hora;
        optionInicio.textContent = hora;
        horaInicioSelect.appendChild(optionInicio);

        const optionFin = document.createElement('option');
        optionFin.value = hora;
        optionFin.textContent = hora;
        horaFinSelect.appendChild(optionFin);
      });
    } catch (error) {
      console.error(error);
    }
  }

  // Función para calcular monto total basado en horas seleccionadas y monto por hora del área
  function calcularMontoTotal() {
    const areaOption = areaSelect.options[areaSelect.selectedIndex];
    if (!areaOption || !horaInicioSelect.value || !horaFinSelect.value) {
      montoTotalInput.value = '0.00';
      return;
    }

    const montoHora = parseFloat(areaOption.dataset.monto);
    const horaInicio = horaInicioSelect.value.split(':');
    const horaFin = horaFinSelect.value.split(':');

    const inicioEnMinutos = parseInt(horaInicio[0]) * 60 + parseInt(horaInicio[1]);
    const finEnMinutos = parseInt(horaFin[0]) * 60 + parseInt(horaFin[1]);

    if (finEnMinutos <= inicioEnMinutos) {
      montoTotalInput.value = '0.00';
      return;
    }

    const duracionHoras = (finEnMinutos - inicioEnMinutos) / 60;
    const total = montoHora * duracionHoras;

    montoTotalInput.value = total.toFixed(2);
  }

  // Eventos para actualizar datos
  areaSelect.addEventListener('change', () => {
    cargarHorasDisponibles();
    calcularMontoTotal();
  });

  fechaInput.addEventListener('change', () => {
    cargarHorasDisponibles();
    calcularMontoTotal();
  });

  horaInicioSelect.addEventListener('change', calcularMontoTotal);
  horaFinSelect.addEventListener('change', calcularMontoTotal);
});
</script>
@endsection
