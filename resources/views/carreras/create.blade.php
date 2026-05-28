@extends('layouts.ap')
@section('title','Nueva Carrera')
@section('content')
<div class="ph"><h1>Registrar Carrera</h1><ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('carreras.index') }}">Carreras</a></li><li>Nueva</li></ol></div>

@if($errors->any())
<div class="al al-d"><i class="fas fa-exclamation-triangle"></i> Hay errores en el formulario. Revisa los campos marcados en rojo.</div>
@endif

<form action="{{ route('carreras.store') }}" method="POST" novalidate>@csrf
<div class="card" style="max-width:600px"><div class="card-hd"><i class="fas fa-graduation-cap"></i>Datos de la carrera</div><div class="card-bd">
<div class="fr c2g">
<div>
  <label class="fl">Nombre <span class="rq">*</span></label>
  <input type="text" name="nombre" class="fc @error('nombre') is-invalid @enderror"
         value="{{ old('nombre') }}" required minlength="3" maxlength="100"
         placeholder="Ej: Ingeniería Informática">
  <p class="fh">Informática · Sistemas · Redes y Telecomunicaciones · Robótica</p>
  @error('nombre')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Sigla</label>
  <input type="text" name="sigla" class="fc @error('sigla') is-invalid @enderror"
         value="{{ old('sigla') }}" minlength="2" maxlength="5"
         pattern="[A-Za-z]{2,5}"
         title="2-5 letras, sin números"
         placeholder="INF" style="text-transform:uppercase">
  @error('sigla')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
</div>
<div style="margin-top:1rem">
  <label class="fl">Descripción</label>
  <textarea name="descripcion" class="fc @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
  @error('descripcion')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div style="margin-top:1rem"><label class="fck"><input type="checkbox" name="estado" value="1" {{ old('estado',1)?'checked':'' }}><span>Carrera activa</span></label></div>
<div style="display:flex;gap:.75rem;margin-top:1.5rem">
<button type="submit" class="btn bp"><i class="fas fa-save"></i> Guardar</button>
<a href="{{ route('carreras.index') }}" class="btn bo2">Cancelar</a></div>
</div></div></form>@endsection
