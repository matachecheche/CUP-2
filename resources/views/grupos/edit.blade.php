@extends('layouts.ap')
@section('title','Editar Grupo')
@section('content')
<div class="ph"><h1>Editar Grupo {{ $grupo->codigo }}</h1>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('grupos.index') }}">Grupos</a></li><li>Editar</li></ol>
</div>

@if($errors->any())
<div class="al al-d"><i class="fas fa-exclamation-triangle"></i> Hay errores en el formulario. Revisa los campos marcados en rojo.</div>
@endif

<form action="{{ route('grupos.update',$grupo) }}" method="POST" novalidate>@csrf @method('PUT')
<div class="card" style="max-width:500px"><div class="card-hd"><i class="fas fa-edit"></i>Editar grupo</div><div class="card-bd">
<div class="fr c2g">
  <div>
    <label class="fl">Turno <span class="rq">*</span></label>
    <select name="turno" class="fs @error('turno') is-invalid @enderror" required>
      @foreach(['mañana','tarde','noche'] as $t)<option value="{{ $t }}" {{ old('turno',$grupo->turno)===$t?'selected':'' }}>{{ ucfirst($t) }}</option>@endforeach
    </select>
    @error('turno')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="fl">Modalidad <span class="rq">*</span></label>
    <select name="modalidad" class="fs @error('modalidad') is-invalid @enderror" required>
      @foreach(['presencial','virtual'] as $m)<option value="{{ $m }}" {{ old('modalidad',$grupo->modalidad)===$m?'selected':'' }}>{{ ucfirst($m) }}</option>@endforeach
    </select>
    @error('modalidad')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="fl">Capacidad máx. <span class="rq">*</span></label>
    <input type="number" name="capacidad_maxima"
           class="fc @error('capacidad_maxima') is-invalid @enderror"
           value="{{ old('capacidad_maxima',$grupo->capacidad_maxima) }}" min="1" max="200" required>
    @error('capacidad_maxima')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
</div>
<label class="fck" style="margin-top:.75rem"><input type="checkbox" name="estado" value="1" {{ $grupo->estado?'checked':'' }}><span>Grupo activo</span></label>
<div style="display:flex;gap:.75rem;margin-top:1.25rem">
  <button type="submit" class="btn bp"><i class="fas fa-save"></i> Guardar</button>
  <a href="{{ route('grupos.show',$grupo) }}" class="btn bo2">Cancelar</a>
</div>
</div></div>
</form>
@endsection
