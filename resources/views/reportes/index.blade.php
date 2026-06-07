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
</form>

<div class="card" style="margin-bottom:1rem;border-left:4px solid #b08a2e"><div class="card-bd" style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
  <div style="flex:1;min-width:240px">
    <div style="font-weight:700"><i class="fas fa-sliders-h" style="margin-right:.45rem"></i>Reporte personalizado (dinámico)</div>
    <div style="font-size:.83rem;color:var(--t3,#8a8678)">Elige una tabla y marca los campos a incluir; el reporte se genera a medida en pantalla, PDF o Excel.</div>
  </div>
  <a class="btn bp" href="{{ route('reportes.show',['personalizado','gestion_id'=>$gestionId]) }}"><i class="fas fa-magic"></i> Construir reporte</a>
</div></div>

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
@endsection
