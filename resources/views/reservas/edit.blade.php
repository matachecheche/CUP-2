@extends('layouts.ap')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Reserva</h2>

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

    <form action="{{ route('reservas.update', $reserva->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="area_comun_id" class="form-label">Área Común</label>
            <select name="area_comun_id" id="area_comun_id" class="form-select" required>
                <option value="">-- Selecciona un área común --</option>
                @foreach ($areasComunes as $area)
                    <option value="{{ $area->id }}" data-monto="{{ $area->monto }}"
                        {{ old('area_comun_id', $reserva->area_comun_id) == $area->id ? 'selected' : '' }}>
                        {{ $area->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" name="fecha" id="fecha" class="form-control"
                value="{{ old('fecha', $reserva->fecha->format('Y-m-d')) }}" required min="{{ date('Y-m-d') }}">
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

        <div class="mb-3">
            <label class="form-label">Monto Total (Bs.)</label>
            <input type="text" id="monto_total" class="form-control" readonly
                value="{{ old('monto_total', number_format($reserva->monto_total, 2)) }}">
        </div>

        <div class="mb-3">
            <label for="observacion" class="form-label">Observación</label>
            <textarea name="observacion" id="observacion" class="form-control">{{ old('observacion', $reserva->observacion) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Reserva</button>
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

    // Valores actuales formateados HH:mm
    const horaInicioActual = "{{ old('hora_inicio', \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i')) }}";
    const horaFinActual = "{{ old('hora_fin', \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i')) }}";

    // Monto guardado desde servidor
    const montoGuardado = parseFloat(montoTotalInput.value.replace(',', ''));

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
            let horas = await response.json();

            // Añadir todas las medias horas desde 8:00 hasta 20:30 para asegurar disponibilidad completa
            const horasCompletas = [];
            for (let h = 8; h <= 20; h++) {
                horasCompletas.push(`${h.toString().padStart(2, '0')}:00`);
                horasCompletas.push(`${h.toString().padStart(2, '0')}:30`);
            }

            // Asegurar que todas las horas completas estén en el array 'horas'
            horasCompletas.forEach(hora => {
                if (!horas.includes(hora)) horas.push(hora);
            });

            // Añadir horas actuales para que siempre aparezcan (editar reserva)
            if (!horas.includes(horaInicioActual)) horas.push(horaInicioActual);
            if (!horas.includes(horaFinActual)) horas.push(horaFinActual);

            // Ordenar horas cronológicamente
            horas.sort((a, b) => {
                const [ah, am] = a.split(':').map(Number);
                const [bh, bm] = b.split(':').map(Number);
                return ah !== bh ? ah - bh : am - bm;
            });

            horaInicioSelect.innerHTML = '<option value="">-- Selecciona hora inicio --</option>';
            horaFinSelect.innerHTML = '<option value="">-- Selecciona hora fin --</option>';

            horas.forEach(hora => {
                let optionInicio = document.createElement('option');
                optionInicio.value = hora;
                optionInicio.textContent = hora;
                horaInicioSelect.appendChild(optionInicio);

                let optionFin = document.createElement('option');
                optionFin.value = hora;
                optionFin.textContent = hora;
                horaFinSelect.appendChild(optionFin);
            });

            // Setear valores actuales
            horaInicioSelect.value = horaInicioActual;
            horaFinSelect.value = horaFinActual;

        } catch (error) {
            console.error(error);
        }
    }

    function calcularMontoTotal() {
        const areaOption = areaSelect.options[areaSelect.selectedIndex];
        if (!areaOption || !horaInicioSelect.value || !horaFinSelect.value) {
            montoTotalInput.value = '0.00';
            return;
        }

        const montoHora = parseFloat(areaOption.dataset.monto);
        if (isNaN(montoHora) || montoHora < 0) {
            montoTotalInput.value = '0.00';
            return;
        }

        const [hiH, hiM] = horaInicioSelect.value.split(':').map(Number);
        const [hfH, hfM] = horaFinSelect.value.split(':').map(Number);

        const inicioMin = hiH * 60 + hiM;
        const finMin = hfH * 60 + hfM;

        const duracionMinutos = finMin - inicioMin;

        if (duracionMinutos <= 0) {
            montoTotalInput.value = '0.00';
            return;
        }

        const duracionHoras = duracionMinutos / 60;
        const total = montoHora * duracionHoras;

        montoTotalInput.value = total.toFixed(2);
    }

    areaSelect.addEventListener('change', () => {
        cargarHorasDisponibles();
        calcularMontoTotal();
    });

    fechaInput.addEventListener('change', () => {
        cargarHorasDisponibles();
        calcularMontoTotal();
    });

    horaInicioSelect.addEventListener('change', () => {
        calcularMontoTotal();
    });

    horaFinSelect.addEventListener('change', () => {
        calcularMontoTotal();
    });

    // Inicializar la carga y cálculo
    cargarHorasDisponibles().then(() => {
        // Solo recalcular si el monto guardado es 0 para no sobreescribir monto previo
        if (montoGuardado <= 0) {
            calcularMontoTotal();
        }
    });
});
</script>
@endsection
