@php $a = $asignacion ?? null; @endphp
@if($errors->any())<div class="al al-d" style="margin-bottom:1rem"><ul style="margin:0;padding-left:1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<div class="card" style="max-width:820px"><div class="card-bd">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:.75rem">
    <div>
      <label class="fl">Materia <span class="rq">*</span></label>
      <select name="materia_id" class="fs @error('materia_id') is-invalid @enderror" required>
        <option value="">— Seleccionar —</option>
        @foreach($materias as $m)<option value="{{ $m->id }}" {{ (string)old('materia_id', $a->materia_id ?? '')===(string)$m->id?'selected':'' }}>{{ $m->nombre }} @if($m->area_formacion)· {{ $m->area_formacion }}@endif</option>@endforeach
      </select>
      @error('materia_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="fl">Docente <span class="rq">*</span></label>
      <select name="docente_id" class="fs @error('docente_id') is-invalid @enderror" required>
        <option value="">— Seleccionar —</option>
        @foreach($docentes as $d)<option value="{{ $d->id }}" {{ (string)old('docente_id', $a->docente_id ?? '')===(string)$d->id?'selected':'' }}>{{ $d->apellidos }}, {{ $d->nombres }} @if($d->area_formacion)· {{ $d->area_formacion }}@endif</option>@endforeach
      </select>
      @error('docente_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
  </div>

  <div style="margin-bottom:.75rem">
    <label class="fl">Grupo <span class="rq">*</span></label>
    <select name="grupo_id" class="fs @error('grupo_id') is-invalid @enderror" required>
      <option value="">— Seleccionar —</option>
      @foreach($grupos as $g)<option value="{{ $g->id }}" {{ (string)old('grupo_id', $a->grupo_id ?? ($grupoSel ?? ''))===(string)$g->id?'selected':'' }}>{{ $g->codigo }} — {{ ucfirst($g->turno) }} ({{ ucfirst($g->modalidad) }})</option>@endforeach
    </select>
    @error('grupo_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:.75rem;margin-bottom:.75rem">
    <div>
      <label class="fl">Día <span class="rq">*</span></label>
      <select name="dia" class="fs @error('dia') is-invalid @enderror" required>
        <option value="">— Día —</option>
        @foreach(['lunes','martes','miercoles','jueves','viernes','sabado'] as $dia)<option value="{{ $dia }}" {{ old('dia', $a->dia ?? '')===$dia?'selected':'' }}>{{ ucfirst($dia) }}</option>@endforeach
      </select>
      @error('dia')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="fl">Hora inicio <span class="rq">*</span></label>
      <input type="time" name="hora_inicio" class="fc @error('hora_inicio') is-invalid @enderror" value="{{ old('hora_inicio', isset($a) ? substr($a->hora_inicio,0,5) : '07:00') }}" required>
      @error('hora_inicio')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="fl">Hora fin <span class="rq">*</span></label>
      <input type="time" name="hora_fin" class="fc @error('hora_fin') is-invalid @enderror" value="{{ old('hora_fin', isset($a) ? substr($a->hora_fin,0,5) : '09:00') }}" required>
      @error('hora_fin')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="fl">Aula</label>
      <input type="text" name="aula" class="fc @error('aula') is-invalid @enderror" value="{{ old('aula', $a->aula ?? '') }}" maxlength="30" pattern="[A-Za-z0-9\-]+" placeholder="Ej: A-101">
      @error('aula')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
  </div>

  <div class="al al-w" style="margin-bottom:.75rem;font-size:.82rem"><i class="fas fa-shield-alt"></i>
    Se <strong>rechazará</strong> si: hay cruce de horario del docente, el docente ya tiene 4 grupos,
    no cumple requisitos (afinidad de área + maestría + diplomado) o la materia ya tiene docente en ese grupo.
  </div>
  <div style="display:flex;gap:.5rem">
    <button type="submit" class="btn bp"><i class="fas fa-save"></i> {{ isset($a) ? 'Actualizar' : 'Asignar docente' }}</button>
    <a href="{{ route('asignaciones.index') }}" class="btn bo2">Cancelar</a>
  </div>
</div></div>
