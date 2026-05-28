@extends('layouts.ap')
@section('title','Editar Postulante')
@section('content')
<div class="ph"><h1>Editar Postulante</h1><p class="sub">Modificar datos antes del cierre de inscripciones</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('postulantes.index') }}">Postulantes</a></li><li>Editar</li></ol></div>

@if($errors->any())
<div class="al al-d"><i class="fas fa-exclamation-triangle"></i> Hay errores en el formulario. Revisa los campos marcados en rojo.</div>
@endif

<form action="{{ route('postulantes.update',$postulante) }}" method="POST" novalidate>@csrf @method('PUT')
<div class="card" style="max-width:840px"><div class="card-hd"><i class="fas fa-user-edit"></i>Editando: {{ $postulante->nombre_completo }}</div><div class="card-bd">
<div class="fs-t">Gestión académica</div>
<div class="fr c2g" style="margin-bottom:1rem">
<div><label class="fl">Gestión <span class="rq">*</span></label>
<select name="gestion_id" class="fs @error('gestion_id') is-invalid @enderror" required>
<option value="">— Seleccionar —</option>
@foreach($gestiones as $g)<option value="{{ $g->id }}" {{ old('gestion_id',$postulante->gestion_id)==$g->id?'selected':'' }}>{{ $g->descripcion }}</option>@endforeach
</select>
@error('gestion_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
</div>

<div class="fs-t">Datos personales</div>
<div class="fr c3g">
<div>
  <label class="fl">CI <span class="rq">*</span></label>
  <input type="text" name="ci" class="fc @error('ci') is-invalid @enderror"
         value="{{ old('ci',$postulante->ci) }}" required maxlength="20"
         pattern="[0-9]{6,10}(-[A-Za-z]{1,2})?">
  @error('ci')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Nombres <span class="rq">*</span></label>
  <input type="text" name="nombres" class="fc @error('nombres') is-invalid @enderror"
         value="{{ old('nombres',$postulante->nombres) }}" required maxlength="100"
         pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s\.\-]+">
  @error('nombres')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Apellidos <span class="rq">*</span></label>
  <input type="text" name="apellidos" class="fc @error('apellidos') is-invalid @enderror"
         value="{{ old('apellidos',$postulante->apellidos) }}" required maxlength="100"
         pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s\.\-]+">
  @error('apellidos')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Fecha de nacimiento <span class="rq">*</span></label>
  <input type="date" name="fecha_nacimiento" class="fc @error('fecha_nacimiento') is-invalid @enderror"
         value="{{ old('fecha_nacimiento',$postulante->fecha_nacimiento?->format('Y-m-d')) }}" required
         max="{{ date('Y-m-d') }}">
  @error('fecha_nacimiento')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Sexo <span class="rq">*</span></label>
  <select name="sexo" class="fs @error('sexo') is-invalid @enderror" required>
    <option value="">— Seleccionar —</option>
    <option value="M" {{ old('sexo',$postulante->sexo)=='M'?'selected':'' }}>Masculino</option>
    <option value="F" {{ old('sexo',$postulante->sexo)=='F'?'selected':'' }}>Femenino</option>
    <option value="Otro" {{ old('sexo',$postulante->sexo)=='Otro'?'selected':'' }}>Otro</option>
  </select>
  @error('sexo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Teléfono</label>
  <input type="tel" name="telefono" class="fc @error('telefono') is-invalid @enderror"
         value="{{ old('telefono',$postulante->telefono) }}" maxlength="8"
         pattern="[67][0-9]{7}">
  @error('telefono')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Correo <span class="rq">*</span></label>
  <input type="email" name="email" class="fc @error('email') is-invalid @enderror"
         value="{{ old('email',$postulante->email) }}" required maxlength="100">
  @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Colegio <span class="rq">*</span></label>
  <input type="text" name="colegio_procedencia" class="fc @error('colegio_procedencia') is-invalid @enderror"
         value="{{ old('colegio_procedencia',$postulante->colegio_procedencia) }}" required maxlength="150">
  @error('colegio_procedencia')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Ciudad <span class="rq">*</span></label>
  <input type="text" name="ciudad" class="fc @error('ciudad') is-invalid @enderror"
         value="{{ old('ciudad',$postulante->ciudad) }}" required maxlength="80">
  @error('ciudad')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
</div>
<div style="margin-top:1rem">
  <label class="fl">Dirección</label>
  <input type="text" name="direccion" class="fc @error('direccion') is-invalid @enderror"
         value="{{ old('direccion',$postulante->direccion) }}" maxlength="200">
  @error('direccion')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>

<div class="fs-t" style="margin-top:1.25rem">Opciones de carrera</div>
<div class="fr c2g">
<div>
  <label class="fl">1ª Opción <span class="rq">*</span></label>
  <select name="primera_opcion_id" id="primera_opcion_id"
          class="fs @error('primera_opcion_id') is-invalid @enderror" required>
    @foreach($carreras as $c)<option value="{{ $c->id }}" {{ old('primera_opcion_id',$postulante->primera_opcion_id)==$c->id?'selected':'' }}>{{ $c->nombre }}</option>@endforeach
  </select>
  @error('primera_opcion_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">2ª Opción <span class="rq">*</span></label>
  <select name="segunda_opcion_id" id="segunda_opcion_id"
          class="fs @error('segunda_opcion_id') is-invalid @enderror" required>
    @foreach($carreras as $c)<option value="{{ $c->id }}" {{ old('segunda_opcion_id',$postulante->segunda_opcion_id)==$c->id?'selected':'' }}>{{ $c->nombre }}</option>@endforeach
  </select>
  @error('segunda_opcion_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
</div>

<div class="fs-t" style="margin-top:1.25rem">Documentos entregados</div>
<div style="display:flex;flex-direction:column;gap:.5rem">
<label class="fck"><input type="checkbox" name="doc_ci" value="1" {{ old('doc_ci',$postulante->doc_ci)?'checked':'' }}><span>CI</span></label>
<label class="fck"><input type="checkbox" name="doc_libreta_colegio" value="1" {{ old('doc_libreta_colegio',$postulante->doc_libreta_colegio)?'checked':'' }}><span>Libreta de colegio</span></label>
<label class="fck"><input type="checkbox" name="doc_titulo_bachiller" value="1" {{ old('doc_titulo_bachiller',$postulante->doc_titulo_bachiller)?'checked':'' }}><span>Título de Bachiller</span></label>
@error('documentos')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>

<div style="display:flex;gap:.75rem;margin-top:1.5rem">
<button type="submit" class="btn bp"><i class="fas fa-save"></i> Actualizar</button>
<a href="{{ route('postulantes.index') }}" class="btn bo2">Cancelar</a></div>
</div></div></form>

@push('js')
<script>
(function(){
  const s1=document.getElementById('primera_opcion_id'), s2=document.getElementById('segunda_opcion_id');
  function chk(){ s2.setCustomValidity(s1.value && s1.value===s2.value ? 'La 2ª opción debe ser diferente a la 1ª opción' : ''); }
  if(s1 && s2){ s1.addEventListener('change',chk); s2.addEventListener('change',chk); }
})();
</script>
@endpush
@endsection
