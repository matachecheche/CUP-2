@extends('layouts.ap')
@section('title','Nueva Gestión')
@section('content')
<div class="ph"><h1>Nueva Gestión Académica</h1><ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('gestiones.index') }}">Gestiones</a></li><li>Nueva</li></ol></div>

@if($errors->any())
<div class="al al-d"><i class="fas fa-exclamation-triangle"></i> Hay errores en el formulario. Revisa los campos marcados en rojo.</div>
@endif

<form action="{{ route('gestiones.store') }}" method="POST" novalidate>@csrf
<div class="card" style="max-width:560px"><div class="card-hd"><i class="fas fa-calendar-plus"></i>Datos de la gestión</div><div class="card-bd">
<div style="margin-bottom:1rem">
  <label class="fl">Descripción <span class="rq">*</span></label>
  <input type="text" name="descripcion" class="fc @error('descripcion') is-invalid @enderror"
         value="{{ old('descripcion') }}" required maxlength="50"
         placeholder="Ej: Semestre 1-2026">
  <p class="fh">Formato sugerido: Semestre 1-2026, Semestre 2-2026, etc.</p>
  @error('descripcion')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div class="fr c2g">
<div>
  <label class="fl">Fecha de inicio <span class="rq">*</span></label>
  <input type="date" name="fecha_inicio" id="fi"
         class="fc @error('fecha_inicio') is-invalid @enderror"
         value="{{ old('fecha_inicio') }}" required>
  @error('fecha_inicio')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Fecha de fin <span class="rq">*</span></label>
  <input type="date" name="fecha_fin" id="ff"
         class="fc @error('fecha_fin') is-invalid @enderror"
         value="{{ old('fecha_fin') }}" required>
  @error('fecha_fin')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
</div>
<div style="margin-top:1rem">
  <label class="fl">Estado <span class="rq">*</span></label>
  <select name="estado" class="fs @error('estado') is-invalid @enderror" required>
    <option value="planificacion" {{ old('estado','planificacion')=='planificacion'?'selected':'' }}>Planificación</option>
    <option value="inscripcion" {{ old('estado')=='inscripcion'?'selected':'' }}>Inscripción</option>
    <option value="en_curso" {{ old('estado')=='en_curso'?'selected':'' }}>En Curso</option>
    <option value="finalizado" {{ old('estado')=='finalizado'?'selected':'' }}>Finalizado</option>
  </select>
  @error('estado')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div style="display:flex;gap:.75rem;margin-top:1.5rem">
<button type="submit" class="btn bp"><i class="fas fa-save"></i> Guardar</button>
<a href="{{ route('gestiones.index') }}" class="btn bo2">Cancelar</a></div>
</div></div></form>
@push('js')<script>
(function(){
  var fi=document.getElementById('fi'),ff=document.getElementById('ff');
  function syn(){ if(fi.value) ff.setAttribute('min',fi.value); }
  fi.addEventListener('change',syn); syn();
})();
</script>@endpush
@endsection
