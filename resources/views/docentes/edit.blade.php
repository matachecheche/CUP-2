@extends('layouts.ap')
@section('title','Editar Docente')
@section('content')
<div class="ph"><h1>Editar Docente</h1><ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('docentes.index') }}">Docentes</a></li><li>Editar</li></ol></div>

@if($errors->any())
<div class="al al-d"><i class="fas fa-exclamation-triangle"></i> Hay errores en el formulario. Revisa los campos marcados en rojo.</div>
@endif

<form action="{{ route('docentes.update',$docente) }}" method="POST" novalidate>@csrf @method('PUT')
<div class="card" style="max-width:780px"><div class="card-hd"><i class="fas fa-edit"></i>Editando: {{ $docente->nombre_completo }}</div><div class="card-bd">
<div class="fs-t">Datos personales</div>
<div class="fr c3g">
<div>
  <label class="fl">CI <span class="rq">*</span></label>
  <input type="text" name="ci" class="fc @error('ci') is-invalid @enderror"
         value="{{ old('ci',$docente->ci) }}" required maxlength="20"
         pattern="[0-9]{6,10}(-[A-Za-z]{1,2})?">
  @error('ci')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Nombres <span class="rq">*</span></label>
  <input type="text" name="nombres" class="fc @error('nombres') is-invalid @enderror"
         value="{{ old('nombres',$docente->nombres) }}" required maxlength="100"
         pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s\.\-]+">
  @error('nombres')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Apellidos <span class="rq">*</span></label>
  <input type="text" name="apellidos" class="fc @error('apellidos') is-invalid @enderror"
         value="{{ old('apellidos',$docente->apellidos) }}" required maxlength="100"
         pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s\.\-]+">
  @error('apellidos')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Teléfono</label>
  <input type="tel" name="telefono" class="fc @error('telefono') is-invalid @enderror"
         value="{{ old('telefono',$docente->telefono) }}" maxlength="8"
         pattern="[67][0-9]{7}">
  @error('telefono')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Email <span class="rq">*</span></label>
  <input type="email" name="email" class="fc @error('email') is-invalid @enderror"
         value="{{ old('email',$docente->email) }}" required maxlength="100">
  @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Área de formación <span class="rq">*</span></label>
  <select name="area_formacion" class="fs @error('area_formacion') is-invalid @enderror" required>
    @foreach(['Computación','Matemáticas','Física','Inglés'] as $a)
      <option value="{{ $a }}" {{ old('area_formacion',$docente->area_formacion)==$a?'selected':'' }}>{{ $a }}</option>
    @endforeach
  </select>
  @error('area_formacion')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
</div>
<div class="fs-t" style="margin-top:1.25rem">Perfil profesional</div>
<div class="fr c2g">
<div>
  <label class="fl">Título profesional <span class="rq">*</span></label>
  <input type="text" name="titulo_profesional" class="fc @error('titulo_profesional') is-invalid @enderror"
         value="{{ old('titulo_profesional',$docente->titulo_profesional) }}" required maxlength="150">
  @error('titulo_profesional')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Maestría <span class="rq">*</span></label>
  <input type="text" name="maestria" class="fc @error('maestria') is-invalid @enderror"
         value="{{ old('maestria',$docente->maestria) }}" required maxlength="150">
  @error('maestria')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Diplomado en Educación Superior <span class="rq">*</span></label>
  <input type="text" name="diplomado_educacion_superior" class="fc @error('diplomado_educacion_superior') is-invalid @enderror"
         value="{{ old('diplomado_educacion_superior',$docente->diplomado_educacion_superior) }}" required maxlength="150">
  @error('diplomado_educacion_superior')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Certificación de Inglés</label>
  <input type="text" name="certificacion_ingles" class="fc @error('certificacion_ingles') is-invalid @enderror"
         value="{{ old('certificacion_ingles',$docente->certificacion_ingles) }}" maxlength="100">
  @error('certificacion_ingles')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
</div>
<div style="margin-top:1rem">
  <label class="fl">Otras certificaciones</label>
  <textarea name="otras_certificaciones" class="fc @error('otras_certificaciones') is-invalid @enderror">{{ old('otras_certificaciones',$docente->otras_certificaciones) }}</textarea>
  @error('otras_certificaciones')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div style="margin-top:1rem"><label class="fck"><input type="checkbox" name="estado" value="1" {{ old('estado',$docente->estado)?'checked':'' }}><span>Activo</span></label></div>
<div style="display:flex;gap:.75rem;margin-top:1.5rem">
<button type="submit" class="btn bp"><i class="fas fa-save"></i> Actualizar</button>
<a href="{{ route('docentes.index') }}" class="btn bo2">Cancelar</a></div>
</div></div></form>@endsection
