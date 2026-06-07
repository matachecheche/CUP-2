@extends('layouts.ap')
@section('title','Reportes')
@section('content')
<div class="ph"><h1>Reportes y Estadísticas</h1><p class="sub">CU-19 — Reportes dinámicos, PDF y Excel del proceso de admisión</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li>Reportes</li></ol></div>

<form method="GET" style="margin-bottom:1.2rem;display:flex;gap:.6rem;align-items:center">
  <label style="font-size:.85rem;color:var(--t3,#8a8678)">Gestión:</label>
  <select name="gestion_id" onchange="this.form.submit()" style="padding:.45rem .6rem;border:1px solid #d8d2c4;border-radius:6px;background:#fff">
    @foreach($gestiones as $g)<option value="{{ $g->id }}" {{ $g->id==$gestionId?'selected':'' }}>{{ $g->descripcion }}</option>@endforeach
  </select>
  <button type="button" id="btnVoz" class="btn bp" title="Consultar por voz (Chrome/Edge)"><i class="fas fa-microphone"></i> Por voz</button>
  <span id="vozTxt" style="font-size:.8rem;color:var(--t3,#8a8678)"></span>
</form>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem">
@foreach($catalogo as $c)
  <div class="card"><div class="card-bd" style="display:flex;flex-direction:column;gap:.5rem">
    <div style="font-weight:700"><i class="fas {{ $c['icono'] }}" style="margin-right:.45rem"></i>{{ $c['nombre'] }}</div>
    <div style="font-size:.83rem;color:var(--t3,#8a8678);flex:1">{{ $c['desc'] }}</div>
    <div style="display:flex;gap:.4rem">
      <a class="btn bsm bp"  href="{{ route('reportes.show',[$c['tipo'],'gestion_id'=>$gestionId]) }}"><i class="fas fa-eye"></i> Ver</a>
      <a class="btn bsm bo2" href="{{ route('reportes.exportar',[$c['tipo'],'pdf','gestion_id'=>$gestionId]) }}"><i class="fas fa-file-pdf"></i> PDF</a>
      <a class="btn bsm bv"  href="{{ route('reportes.exportar',[$c['tipo'],'csv','gestion_id'=>$gestionId]) }}"><i class="fas fa-file-excel"></i> Excel</a>
    </div>
  </div></div>
@endforeach
</div>

@push('js')<script>
const mapaVoz = {general:['general','lista','postulantes'],aprobados:['aprobado'],reprobados:['reprobado'],
  promedios:['promedio'],grupos:['grupos habilitados','ocupación','grupos'],materias:['materia'],
  docentes:['docente','carga'],['top-grupos']:['mejores','mayor','ranking','top']};
const btn=document.getElementById('btnVoz'),txt=document.getElementById('vozTxt');
const SR=window.SpeechRecognition||window.webkitSpeechRecognition;
if(!SR){btn.style.display='none'}else{
  const rec=new SR();rec.lang='es-ES';rec.interimResults=false;
  btn.onclick=()=>{txt.textContent='Escuchando… diga p.ej. "postulantes aprobados"';rec.start()};
  rec.onresult=e=>{const dicho=e.results[0][0].transcript.toLowerCase();txt.textContent='"'+dicho+'"';
    const pdf=dicho.includes('pdf'),excel=dicho.includes('excel')||dicho.includes('csv');
    for(const [tipo,claves] of Object.entries(mapaVoz)){
      if(claves.some(k=>dicho.includes(k))){
        const base='{{ url('reportes') }}/'+tipo, q='?gestion_id={{ $gestionId }}';
        location.href = pdf?base+'/exportar/pdf'+q : excel?base+'/exportar/csv'+q : base+q; return;}}
    txt.textContent='No entendí: "'+dicho+'". Pruebe "aprobados", "promedios", "docentes"…';};
  rec.onerror=()=>txt.textContent='Micrófono no disponible o permiso denegado.';}
</script>@endpush
@endsection
