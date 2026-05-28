@extends('layouts.ap')
@section('title','Registrar Docente')
@section('content')
<div class="ph"><h1>Registrar Docente</h1><p class="sub">CU-10 — Requisitos: título profesional, maestría y diplomado en educación superior</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('docentes.index') }}">Docentes</a></li><li>Registrar</li></ol></div>

@if($errors->any())
<div class="al al-d"><i class="fas fa-exclamation-triangle"></i> Hay errores en el formulario. Revisa los campos marcados en rojo.</div>
@endif

<form action="{{ route('docentes.store') }}" method="POST" novalidate>@csrf
<div class="card" style="max-width:780px"><div class="card-hd"><i class="fas fa-chalkboard-teacher"></i>Perfil del docente</div><div class="card-bd">
<div class="fs-t">Datos personales</div>
<div class="fr c3g">
<div>
  <label class="fl">CI <span class="rq">*</span></label>
  <input type="text" name="ci" class="fc @error('ci') is-invalid @enderror"
         value="{{ old('ci') }}" required maxlength="20"
         pattern="[0-9]{6,10}(-[A-Za-z]{1,2})?"
         title="6-10 dígitos, opcional sufijo -LP, -SC, etc."
         placeholder="Ej: 12345678">
  @error('ci')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Nombres <span class="rq">*</span></label>
  <input type="text" name="nombres" class="fc @error('nombres') is-invalid @enderror"
         value="{{ old('nombres') }}" required maxlength="100"
         pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s\.\-]+">
  @error('nombres')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Apellidos <span class="rq">*</span></label>
  <input type="text" name="apellidos" class="fc @error('apellidos') is-invalid @enderror"
         value="{{ old('apellidos') }}" required maxlength="100"
         pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s\.\-]+">
  @error('apellidos')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Teléfono</label>
  <input type="tel" name="telefono" class="fc @error('telefono') is-invalid @enderror"
         value="{{ old('telefono') }}" maxlength="8"
         pattern="[67][0-9]{7}"
         placeholder="Ej: 70123456">
  @error('telefono')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Correo electrónico <span class="rq">*</span></label>
  <input type="email" name="email" class="fc @error('email') is-invalid @enderror"
         value="{{ old('email') }}" required maxlength="100">
  @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Área de formación <span class="rq">*</span></label>
  <select name="area_formacion" class="fs @error('area_formacion') is-invalid @enderror" required>
    <option value="">— Seleccionar —</option>
    @foreach(['Computación','Matemáticas','Física','Inglés'] as $a)
      <option value="{{ $a }}" {{ old('area_formacion')==$a?'selected':'' }}>{{ $a }}</option>
    @endforeach
  </select>
  <p class="fh">Determina qué materias puede dictar (CU-10).</p>
  @error('area_formacion')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
</div>

<div class="fs-t" style="margin-top:1.25rem">Perfil profesional (requisitos obligatorios de contratación)</div>
<div class="al al-w" style="margin-bottom:1rem"><i class="fas fa-info-circle"></i> Los tres primeros campos son obligatorios según el reglamento de contratación del CUP.</div>
<div class="fr c2g">
<div>
  <label class="fl">Título profesional <span class="rq">*</span></label>
  <input type="text" name="titulo_profesional" class="fc @error('titulo_profesional') is-invalid @enderror"
         value="{{ old('titulo_profesional') }}" required maxlength="150"
         placeholder="Ej: Ing. en Sistemas de Información">
  @error('titulo_profesional')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Maestría <span class="rq">*</span></label>
  <input type="text" name="maestria" class="fc @error('maestria') is-invalid @enderror"
         value="{{ old('maestria') }}" required maxlength="150"
         placeholder="Ej: Maestría en Tecnologías de la Información">
  @error('maestria')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Diplomado en Educación Superior <span class="rq">*</span></label>
  <input type="text" name="diplomado_educacion_superior" class="fc @error('diplomado_educacion_superior') is-invalid @enderror"
         value="{{ old('diplomado_educacion_superior') }}" required maxlength="150"
         placeholder="Ej: Diplomado en Docencia Universitaria">
  @error('diplomado_educacion_superior')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Certificación de Inglés</label>
  <input type="text" name="certificacion_ingles" class="fc @error('certificacion_ingles') is-invalid @enderror"
         value="{{ old('certificacion_ingles') }}" maxlength="100"
         placeholder="Ej: TOEFL 550, Cambridge B2">
  @error('certificacion_ingles')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
</div>
<div style="margin-top:1rem">
  <label class="fl">Otras certificaciones</label>
  <textarea name="otras_certificaciones" class="fc @error('otras_certificaciones') is-invalid @enderror"
            placeholder="Cursos, diplomados adicionales, certificaciones técnicas...">{{ old('otras_certificaciones') }}</textarea>
  @error('otras_certificaciones')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div style="margin-top:1rem"><label class="fck"><input type="checkbox" name="estado" value="1" {{ old('estado',1)?'checked':'' }}><span>Docente activo</span></label></div>
<div style="display:flex;gap:.75rem;margin-top:1.5rem">
<button type="submit" class="btn bp"><i class="fas fa-save"></i> Registrar Docente</button>
<a href="{{ route('docentes.index') }}" class="btn bo2">Cancelar</a></div>
</div></div></form>@endsection
