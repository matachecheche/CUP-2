@extends('layouts.ap')
@section('title','Editar Materia')
@section('content')
<div class="ph"><h1>Editar Materia: {{ $materia->nombre }}</h1>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('materias.index') }}">Materias</a></li><li>Editar</li></ol></div>

@if($errors->any())
<div class="al al-d"><i class="fas fa-exclamation-triangle"></i> Hay errores en el formulario. Revisa los campos marcados en rojo.</div>
@endif

<form action="{{ route('materias.update',$materia) }}" method="POST" novalidate>@csrf @method('PUT')
<div class="card" style="max-width:620px"><div class="card-hd"><i class="fas fa-edit"></i>Editando: {{ $materia->nombre }}</div><div class="card-bd">
<div class="fr c2g">
<div>
  <label class="fl">Nombre <span class="rq">*</span></label>
  <input type="text" name="nombre" class="fc @error('nombre') is-invalid @enderror"
         value="{{ old('nombre',$materia->nombre) }}" required maxlength="100">
  @error('nombre')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Área de formación <span class="rq">*</span></label>
  <select name="area_formacion" class="fs @error('area_formacion') is-invalid @enderror" required>
    @foreach(['Computación','Matemáticas','Física','Inglés'] as $a)
      <option value="{{ $a }}" {{ old('area_formacion',$materia->area_formacion)==$a?'selected':'' }}>{{ $a }}</option>
    @endforeach
  </select>
  @error('area_formacion')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
</div>
<div style="margin-top:1rem">
  <label class="fl">Descripción</label>
  <textarea name="descripcion" class="fc @error('descripcion') is-invalid @enderror">{{ old('descripcion',$materia->descripcion) }}</textarea>
  @error('descripcion')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div style="margin-top:1.25rem"><div class="fs-t">Ponderación de los 3 exámenes</div>
<div class="fr c3g">
<div>
  <label class="fl">Examen 1 (%) <span class="rq">*</span></label>
  <input type="number" name="pond_examen1" id="p1"
         class="fc @error('pond_examen1') is-invalid @enderror @error('pond_total') is-invalid @enderror"
         value="{{ old('pond_examen1',$materia->pond_examen1) }}" min="1" max="98" required>
  @error('pond_examen1')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Examen 2 (%) <span class="rq">*</span></label>
  <input type="number" name="pond_examen2" id="p2"
         class="fc @error('pond_examen2') is-invalid @enderror @error('pond_total') is-invalid @enderror"
         value="{{ old('pond_examen2',$materia->pond_examen2) }}" min="1" max="98" required>
  @error('pond_examen2')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Examen 3 (%) <span class="rq">*</span></label>
  <input type="number" name="pond_examen3" id="p3"
         class="fc @error('pond_examen3') is-invalid @enderror @error('pond_total') is-invalid @enderror"
         value="{{ old('pond_examen3',$materia->pond_examen3) }}" min="1" max="98" required>
  @error('pond_examen3')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
</div>
<div id="ptot" style="margin-top:.5rem;font-size:.85rem;font-weight:600"></div>
@error('pond_total')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div class="fr c2g" style="margin-top:1rem">
<div>
  <label class="fl">Nota mínima <span class="rq">*</span></label>
  <input type="number" name="nota_minima_aprobacion"
         class="fc @error('nota_minima_aprobacion') is-invalid @enderror"
         value="{{ old('nota_minima_aprobacion',$materia->nota_minima_aprobacion) }}" min="1" max="100" required>
  @error('nota_minima_aprobacion')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
<div>
  <label class="fl">Orden</label>
  <input type="number" name="orden" class="fc @error('orden') is-invalid @enderror"
         value="{{ old('orden',$materia->orden) }}" min="0">
  @error('orden')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
</div>
<div style="margin-top:1rem"><label class="fck"><input type="checkbox" name="estado" value="1" {{ old('estado',$materia->estado)?'checked':'' }}><span>Activa</span></label></div>
<div style="display:flex;gap:.75rem;margin-top:1.5rem">
<button type="submit" class="btn bp"><i class="fas fa-save"></i> Actualizar</button>
<a href="{{ route('materias.index') }}" class="btn bo2">Cancelar</a></div>
</div></div></form>
@push('js')<script>
function upd(){
  var p1=document.getElementById('p1'),p2=document.getElementById('p2'),p3=document.getElementById('p3');
  var s=(+p1.value||0)+(+p2.value||0)+(+p3.value||0);
  var e=document.getElementById('ptot');
  e.textContent='Total: '+s+'%';
  var ok=(s===100);
  e.style.color=ok?'var(--v3)':'var(--d)';
  [p1,p2,p3].forEach(function(i){
    if(ok){i.classList.remove('is-invalid');i.setCustomValidity('');}
    else {i.classList.add('is-invalid');i.setCustomValidity('Las ponderaciones deben sumar 100%');}
  });
}
['p1','p2','p3'].forEach(function(i){document.getElementById(i).addEventListener('input',upd)});upd();
</script>@endpush
@endsection
