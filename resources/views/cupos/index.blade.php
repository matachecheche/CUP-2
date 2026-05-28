@extends('layouts.ap')
@section('title','Cupos por Carrera y Gestión')

@section('content')
<div class="ph">
  <h1>Cupos por Carrera y Gestión</h1>
  <p class="sub">CU-08 — Cuántos alumnos puede admitir cada carrera por semestre</p>
  <ol class="bc">
    <li><a href="{{ route('panel') }}">Inicio</a></li>
    <li><a href="{{ route('carreras.index') }}">Carreras</a></li>
    <li>Cupos</li>
  </ol>
</div>

{{-- Formulario para definir / editar un cupo --}}
<div class="card" style="max-width:640px; margin-bottom:1.5rem">
  <div class="card-hd"><i class="fas fa-sliders-h"></i> Definir / actualizar cupo</div>
  <div class="card-bd">
    @if($errors->any())
    <div class="al al-d" style="margin-bottom:1rem"><i class="fas fa-exclamation-triangle"></i> Hay errores en el formulario. Revisa los campos marcados en rojo.</div>
    @endif
    <form action="{{ route('cupos.store') }}" method="POST" novalidate>
      @csrf
      <div class="fr c3g">
        <div>
          <label class="fl">Carrera <span class="rq">*</span></label>
          <select name="carrera_id" class="fs @error('carrera_id') is-invalid @enderror" required id="sel-carrera">
            <option value="">— Seleccionar —</option>
            @foreach($carreras as $c)
              <option value="{{ $c->id }}" {{ old('carrera_id')==$c->id ? 'selected':'' }}>
                {{ $c->sigla }} — {{ $c->nombre }}
              </option>
            @endforeach
          </select>
          @error('carrera_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="fl">Gestión <span class="rq">*</span></label>
          <select name="gestion_id" class="fs @error('gestion_id') is-invalid @enderror" required id="sel-gestion">
            <option value="">— Seleccionar —</option>
            @foreach($gestiones as $g)
              <option value="{{ $g->id }}" {{ old('gestion_id')==$g->id ? 'selected':'' }}>
                {{ $g->descripcion }}
              </option>
            @endforeach
          </select>
          @error('gestion_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="fl">Cupo máximo <span class="rq">*</span></label>
          <input type="number" name="cantidad_maxima"
                 class="fc @error('cantidad_maxima') is-invalid @enderror" id="inp-cupo"
                 min="1" max="9999" value="{{ old('cantidad_maxima', 60) }}" required>
          @error('cantidad_maxima')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="al al-w" style="margin:.75rem 0 .75rem">
        <i class="fas fa-info-circle"></i>
        Si ya existe un cupo para esa combinación, se <strong>actualiza</strong> automáticamente.
        Haz clic en cualquier celda de la tabla para pre-cargar el formulario.
      </div>
      <button type="submit" class="btn bp"><i class="fas fa-save"></i> Guardar cupo</button>
    </form>
  </div>
</div>

{{-- Tabla cruzada carrera × gestión --}}
<div class="card">
  <div class="card-hd"><i class="fas fa-table"></i> Cupos definidos</div>
  <div class="card-bd">
    @if($gestiones->isEmpty() || $carreras->isEmpty())
      <div class="al al-w">
        <i class="fas fa-exclamation-triangle"></i>
        Sin datos suficientes. Primero crea
        @if($gestiones->isEmpty()) <a href="{{ route('gestiones.create') }}">gestiones académicas</a> @endif
        @if($carreras->isEmpty()) <a href="{{ route('carreras.create') }}">carreras</a> @endif.
      </div>
    @else
    <div style="overflow-x:auto">
      <table class="ct">
        <thead>
          <tr>
            <th>Carrera</th>
            @foreach($gestiones as $g)
              <th style="text-align:center; white-space:nowrap">{{ $g->descripcion }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($carreras as $c)
          <tr>
            <td>
              <strong>{{ $c->sigla }}</strong>
              <span style="color:var(--t3); font-size:.84rem"> — {{ $c->nombre }}</span>
            </td>
            @foreach($gestiones as $g)
              <td style="text-align:center">
                @if(isset($matriz[$c->id][$g->id]))
                  <span class="bg bv" style="cursor:pointer; min-width:42px; display:inline-flex; justify-content:center"
                        title="Cupo: {{ $matriz[$c->id][$g->id] }} — clic para editar"
                        onclick="cargar({{ $c->id }}, {{ $g->id }}, {{ $matriz[$c->id][$g->id] }})">
                    {{ $matriz[$c->id][$g->id] }}
                  </span>
                @else
                  <span class="bg bg2" style="cursor:pointer; min-width:42px; display:inline-flex; justify-content:center"
                        title="Sin cupo — clic para agregar"
                        onclick="cargar({{ $c->id }}, {{ $g->id }}, 60)">
                    —
                  </span>
                @endif
              </td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <p style="font-size:.78rem; color:var(--t3); margin-top:.6rem">
      <i class="fas fa-mouse-pointer"></i>
      Verde = cupo definido · Gris = sin cupo. Haz clic en una celda para editar.
    </p>
    @endif
  </div>
</div>

<div style="margin-top:1rem">
  <a href="{{ route('carreras.index') }}" class="btn bo2">
    <i class="fas fa-arrow-left"></i> Volver a Carreras
  </a>
</div>

@push('js')
<script>
function cargar(cid, gid, cupo) {
  document.getElementById('sel-carrera').value = cid;
  document.getElementById('sel-gestion').value = gid;
  document.getElementById('inp-cupo').value    = cupo;
  document.getElementById('inp-cupo').focus();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endpush
@endsection
