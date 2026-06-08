@extends('layouts.ap')
@section('title','Nuevo Grupo')
@section('content')
<div class="ph"><h1>Nuevo Grupo</h1><p class="sub">CU-11 · Crear un grupo manualmente — {{ $gestion->descripcion ?? 'sin gestión activa' }}</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('grupos.index') }}">Grupos</a></li><li>Nuevo</li></ol></div>

@if($errors->any())<div class="al al-d" style="margin-bottom:1rem"><ul style="margin:0;padding-left:1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

@if(!$gestion)
<div class="al al-w"><i class="fas fa-exclamation-triangle"></i> No hay gestión activa. <a href="{{ route('gestiones.index') }}">Activa una gestión</a> antes de crear grupos.</div>
@else
<form action="{{ route('grupos.store') }}" method="POST">@csrf
<div class="card" style="max-width:560px"><div class="card-bd" style="display:flex;flex-direction:column;gap:.9rem">
  <div>
    <label class="fl">Código <span class="rq">*</span></label>
    <input type="text" name="codigo" class="fc @error('codigo') is-invalid @enderror" value="{{ old('codigo') }}" maxlength="20" required placeholder="Ej: GRP-A">
    @error('codigo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
  <div class="fr c2g" style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
    <div>
      <label class="fl">Turno <span class="rq">*</span></label>
      <select name="turno" class="fs">@foreach(['mañana','tarde','noche'] as $t)<option value="{{ $t }}" {{ old('turno')===$t?'selected':'' }}>{{ ucfirst($t) }}</option>@endforeach</select>
    </div>
    <div>
      <label class="fl">Modalidad <span class="rq">*</span></label>
      <select name="modalidad" class="fs">@foreach(['presencial','virtual'] as $m)<option value="{{ $m }}" {{ old('modalidad')===$m?'selected':'' }}>{{ ucfirst($m) }}</option>@endforeach</select>
    </div>
  </div>
  <div>
    <label class="fl">Capacidad máxima <span class="rq">*</span></label>
    <input type="number" name="capacidad_maxima" class="fc @error('capacidad_maxima') is-invalid @enderror" value="{{ old('capacidad_maxima', 70) }}" min="1" max="200" required>
    @error('capacidad_maxima')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
  <div style="display:flex;gap:.5rem">
    <button type="submit" class="btn bp"><i class="fas fa-save"></i> Crear grupo</button>
    <a href="{{ route('grupos.index') }}" class="btn bo2">Cancelar</a>
  </div>
</div></div>
</form>
@endif
@endsection
