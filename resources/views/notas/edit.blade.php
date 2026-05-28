@extends('layouts.ap')
@section('title','Editar Nota')
@section('content')
<div class="ph"><h1>Editar Nota</h1>
<p class="sub">{{ $nota->postulante?->nombre_completo }} · {{ $nota->materia?->nombre }}</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('notas.index',['grupo_id'=>$nota->grupo_id,'materia_id'=>$nota->materia_id]) }}">Notas</a></li><li>Editar</li></ol>
</div>

@if($errors->any())
<div class="al al-d"><i class="fas fa-exclamation-triangle"></i> Hay errores en el formulario. Revisa los campos marcados en rojo.</div>
@endif

<form action="{{ route('notas.update',$nota) }}" method="POST" novalidate>@csrf @method('PUT')
<div class="card" style="max-width:500px"><div class="card-hd"><i class="fas fa-edit"></i>Exámenes</div><div class="card-bd">
<div class="fr c3g">
  <div>
    <label class="fl">Examen 1 <span class="rq">*</span></label>
    <input type="number" name="examen1" id="e1"
           class="fc @error('examen1') is-invalid @enderror"
           value="{{ old('examen1',$nota->examen1) }}" min="0" max="100" step="0.01" required>
    @error('examen1')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="fl">Examen 2 <span class="rq">*</span></label>
    <input type="number" name="examen2" id="e2"
           class="fc @error('examen2') is-invalid @enderror"
           value="{{ old('examen2',$nota->examen2) }}" min="0" max="100" step="0.01" required>
    @error('examen2')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="fl">Examen 3 <span class="rq">*</span></label>
    <input type="number" name="examen3" id="e3"
           class="fc @error('examen3') is-invalid @enderror"
           value="{{ old('examen3',$nota->examen3) }}" min="0" max="100" step="0.01" required>
    @error('examen3')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
</div>
<div class="al al-w" style="margin-top:.75rem"><i class="fas fa-calculator"></i> Nota actual: <strong>{{ $nota->nota_final }}</strong> — {{ $nota->aprobado?'Aprobado':'Reprobado' }}</div>
<div style="display:flex;gap:.75rem;margin-top:1.25rem">
  <button type="submit" class="btn bp"><i class="fas fa-save"></i> Guardar cambios</button>
  <a href="{{ route('notas.index',['grupo_id'=>$nota->grupo_id,'materia_id'=>$nota->materia_id]) }}" class="btn bo2">Cancelar</a>
</div>
</div></div>
</form>
@endsection
