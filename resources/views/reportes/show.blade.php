@extends('layouts.ap')
@section('title', $titulo)
@push('css')<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">@endpush
@section('content')
<div class="ph"><h1>{{ $titulo }}</h1><p class="sub">{{ $subtitulo }}</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('reportes.index') }}">Reportes</a></li><li>{{ $titulo }}</li></ol></div>

<form method="GET" style="margin-bottom:1rem;display:flex;gap:.6rem;flex-wrap:wrap;align-items:center">
  <select name="gestion_id" onchange="this.form.submit()" style="padding:.45rem .6rem;border:1px solid #d8d2c4;border-radius:6px;background:#fff">
    @foreach($gestiones as $g)<option value="{{ $g->id }}" {{ request('gestion_id')==$g->id?'selected':'' }}>{{ $g->descripcion }}</option>@endforeach
  </select>
  @if($tipo === 'general')
  <select name="carrera_id" onchange="this.form.submit()" style="padding:.45rem .6rem;border:1px solid #d8d2c4;border-radius:6px;background:#fff">
    <option value="">— Todas las carreras —</option>
    @foreach($carreras as $c)<option value="{{ $c->id }}" {{ request('carrera_id')==$c->id?'selected':'' }}>{{ $c->nombre }}</option>@endforeach
  </select>
  <select name="estado" onchange="this.form.submit()" style="padding:.45rem .6rem;border:1px solid #d8d2c4;border-radius:6px;background:#fff">
    <option value="">— Todos los estados —</option>
    @foreach(['preinscrito','inscrito','en_curso','aprobado','no_aprobado','admitido','admitido_segunda_opcion','no_admitido'] as $e)
      <option value="{{ $e }}" {{ request('estado')===$e?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$e)) }}</option>
    @endforeach
  </select>
  @endif
  <span style="flex:1"></span>
  <a class="btn bsm bo2" href="{{ route('reportes.exportar',[$tipo,'pdf']+request()->query()) }}"><i class="fas fa-file-pdf"></i> PDF</a>
  <a class="btn bsm bv"  href="{{ route('reportes.exportar',[$tipo,'csv']+request()->query()) }}"><i class="fas fa-file-excel"></i> Excel</a>
</form>

@if($grafico)
<div class="card" style="margin-bottom:1rem"><div class="card-bd" style="max-width:680px;margin:auto">
  <canvas id="chart"></canvas>
</div></div>
@endif

<div class="card"><div class="card-hd"><i class="fas fa-table"></i>{{ $titulo }} ({{ count($filas) }} registros)</div><div class="card-bd">
<div class="tw"><table id="trep" class="ct" style="width:100%">
<thead><tr>@foreach($columnas as $col)<th>{{ $col }}</th>@endforeach</tr></thead>
<tbody>@foreach($filas as $f)<tr>@foreach($f as $v)<td>{{ $v }}</td>@endforeach</tr>@endforeach</tbody>
</table></div></div></div>

@push('js')<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
$(()=>$('#trep').DataTable({language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'},pageLength:25}));
@if($grafico)
new Chart(document.getElementById('chart'),{type:@json($grafico['tipo']),
  data:{labels:@json($grafico['labels']),datasets:[{label:@json($grafico['label']),data:@json($grafico['data']),
    backgroundColor:['#1d3b2a','#b08a2e','#7d2c2c','#3a5b7d','#5b7d3a','#7d5b3a','#3a7d6e','#6e3a7d']}]},
  options:{plugins:{legend:{display: @json($grafico['tipo']==='doughnut') }},responsive:true}});
@endif
</script>@endpush
@endsection
