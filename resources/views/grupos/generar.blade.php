@extends('layouts.ap')
@section('title','Generar grupos')
@section('content')
<div class="ph"><h1>Generar grupos automáticamente</h1>
<p class="sub">CU-11 · Define turno, capacidad y modalidad; se crearán ⌈inscritos sin grupo ÷ capacidad⌉ grupos y se distribuyen</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('grupos.index') }}">Grupos</a></li><li>Generar</li></ol></div>

@if($errors->any())<div class="al al-d" style="margin-bottom:1rem"><ul style="margin:0;padding-left:1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

@if(!$gestion)
<div class="al al-w"><i class="fas fa-exclamation-triangle"></i> No hay ninguna gestión disponible. <a href="{{ route('gestiones.index') }}">Crea una gestión</a> primero.</div>
@else
{{-- Paso 1: gestión (recarga y recalcula los inscritos sin grupo) --}}
<form method="GET" action="{{ route('grupos.generar.form') }}" style="margin-bottom:1rem;display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
  <label class="fl" style="margin:0">Gestión:</label>
  <select name="gestion_id" onchange="this.form.submit()" style="padding:.45rem .6rem;border:1px solid #d8d2c4;border-radius:6px;background:#fff">
    @foreach($gestiones as $g)<option value="{{ $g->id }}" {{ $g->id===$gestion->id?'selected':'' }}>{{ $g->descripcion }}@if($g->estado==='en_curso') (activa)@endif</option>@endforeach
  </select>
  <span style="font-size:.82rem;color:var(--t3,#8a8678)">Inscritos sin grupo: <strong>{{ $totalInscritos }}</strong></span>
</form>

{{-- Paso 2: parámetros del lote + preview en vivo --}}
<form method="POST" action="{{ route('grupos.generar') }}">@csrf
  <input type="hidden" name="gestion_id" value="{{ $gestion->id }}">
  <input type="hidden" id="totalInscritos" value="{{ $totalInscritos }}">
  <div class="card" style="max-width:600px"><div class="card-bd" style="display:flex;flex-direction:column;gap:.9rem">
    <div>
      <label class="fl">Turno <span class="rq">*</span></label>
      <select name="turno" class="fs" required>
        @foreach(['mañana'=>'Mañana','tarde'=>'Tarde','noche'=>'Noche'] as $v=>$l)<option value="{{ $v }}" {{ old('turno')===$v?'selected':'' }}>{{ $l }}</option>@endforeach
      </select>
    </div>
    <div>
      <label class="fl">Estudiantes por grupo (capacidad) <span class="rq">*</span></label>
      <input type="number" name="capacidad" id="capacidad" class="fc @error('capacidad') is-invalid @enderror" min="1" max="200" value="{{ old('capacidad', 60) }}" required>
      @error('capacidad')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="fl">Modalidad <span class="rq">*</span></label>
      <select name="modalidad" class="fs" required>
        @foreach(['presencial'=>'Presencial','virtual'=>'Virtual'] as $v=>$l)<option value="{{ $v }}" {{ old('modalidad')===$v?'selected':'' }}>{{ $l }}</option>@endforeach
      </select>
    </div>
    <div class="al al-w" id="preview" style="margin:0;display:block;line-height:1.5"></div>
    <div style="display:flex;gap:.5rem">
      <button type="submit" class="btn bp" id="btnGenerar"><i class="fas fa-magic"></i> Generar grupos</button>
      <a href="{{ route('grupos.index') }}" class="btn bo2">Cancelar</a>
    </div>
  </div></div>
</form>

<script>
(function(){
  const total    = parseInt(document.getElementById('totalInscritos').value, 10) || 0;
  const capInput = document.getElementById('capacidad');
  const preview  = document.getElementById('preview');
  const btn      = document.getElementById('btnGenerar');
  function actualizarPreview(){
    const cap = parseInt(capInput.value, 10);
    if (!cap || cap < 1) { preview.textContent = 'Ingresa una capacidad válida (≥ 1).'; btn.disabled = true; return; }
    const grupos = Math.ceil(total / cap);          // mismo cálculo estricto que el backend
    const ultimo = total - (grupos - 1) * cap;      // estudiantes en el último grupo
    btn.disabled = total < 1;
    if (total < 1) {
      preview.innerHTML = 'No hay inscritos sin grupo para esta gestión.';
    } else if (grupos === 1) {
      preview.innerHTML = 'Se creará <strong>1</strong> grupo con capacidad <strong>'+cap+'</strong> para los <strong>'+total+'</strong> inscritos sin grupo.';
    } else if (ultimo === cap) {
      preview.innerHTML = 'Se crearán <strong>'+grupos+'</strong> grupos de <strong>'+cap+'</strong> estudiantes cada uno (<strong>'+total+'</strong> inscritos en total).';
    } else {
      preview.innerHTML = 'Se crearán <strong>'+grupos+'</strong> grupos para repartir los <strong>'+total+'</strong> inscritos sin grupo: '
        + '<strong>'+(grupos-1)+'</strong> grupo(s) de <strong>'+cap+'</strong> y el último con <strong>'+ultimo+'</strong> estudiante(s).';
    }
  }
  capInput.addEventListener('input', actualizarPreview);
  actualizarPreview();
})();
</script>
@endif
@endsection
