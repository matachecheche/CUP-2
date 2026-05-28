@extends('layouts.ap')
@section('title','Registrar Nota')
@section('content')
<div class="ph"><h1>Registrar Nota</h1>
<p class="sub">CU-13 — {{ $postulante->nombre_completo }} · {{ $materia->nombre }}</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('notas.index',['grupo_id'=>$grupo->id,'materia_id'=>$materia->id]) }}">Notas</a></li><li>Registrar</li></ol>
</div>

@if($errors->any())
<div class="al al-d"><i class="fas fa-exclamation-triangle"></i> Hay errores en el formulario. Revisa los campos marcados en rojo.</div>
@endif

<form action="{{ route('notas.store') }}" method="POST" novalidate>@csrf
<input type="hidden" name="postulante_id" value="{{ $postulante->id }}">
<input type="hidden" name="grupo_id"      value="{{ $grupo->id }}">
<input type="hidden" name="materia_id"    value="{{ $materia->id }}">
<div class="card" style="max-width:500px"><div class="card-hd"><i class="fas fa-pencil-alt"></i>Exámenes — ponderación {{ $materia->pond_examen1 }}%+{{ $materia->pond_examen2 }}%+{{ $materia->pond_examen3 }}%</div><div class="card-bd">
<div class="fr c3g">
  <div>
    <label class="fl">Examen 1 ({{ $materia->pond_examen1 }}%) <span class="rq">*</span></label>
    <input type="number" name="examen1" id="e1"
           class="fc @error('examen1') is-invalid @enderror"
           value="{{ old('examen1') }}" min="0" max="100" step="0.01" required>
    @error('examen1')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="fl">Examen 2 ({{ $materia->pond_examen2 }}%) <span class="rq">*</span></label>
    <input type="number" name="examen2" id="e2"
           class="fc @error('examen2') is-invalid @enderror"
           value="{{ old('examen2') }}" min="0" max="100" step="0.01" required>
    @error('examen2')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="fl">Examen 3 ({{ $materia->pond_examen3 }}%) <span class="rq">*</span></label>
    <input type="number" name="examen3" id="e3"
           class="fc @error('examen3') is-invalid @enderror"
           value="{{ old('examen3') }}" min="0" max="100" step="0.01" required>
    @error('examen3')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  </div>
</div>
@error('postulante_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
<div class="al al-w" style="margin-top:.75rem"><i class="fas fa-calculator"></i> Nota final calculada: <strong id="nf">—</strong>
  (mínimo aprobación: {{ $materia->nota_minima_aprobacion }})</div>
<div style="display:flex;gap:.75rem;margin-top:1.25rem">
  <button type="submit" class="btn bp"><i class="fas fa-save"></i> Registrar nota</button>
  <a href="{{ route('notas.index',['grupo_id'=>$grupo->id,'materia_id'=>$materia->id]) }}" class="btn bo2">Cancelar</a>
</div>
</div></div>
</form>
@push('js')<script>
(function(){
  var w1={{ $materia->pond_examen1 }}/100, w2={{ $materia->pond_examen2 }}/100, w3={{ $materia->pond_examen3 }}/100;
  var e1=document.getElementById('e1'), e2=document.getElementById('e2'), e3=document.getElementById('e3'), nf=document.getElementById('nf');
  function calc(){
    var v1=+e1.value||0, v2=+e2.value||0, v3=+e3.value||0;
    var t=v1*w1+v2*w2+v3*w3;
    nf.textContent = t.toFixed(2);
    nf.style.color = t>={{ $materia->nota_minima_aprobacion }} ? 'var(--v3)' : 'var(--d)';
  }
  [e1,e2,e3].forEach(function(x){x.addEventListener('input',calc)}); calc();
})();
</script>@endpush
@endsection
