@extends('layouts.ap')
@section('title','Reporte personalizado')
@section('content')
<div class="ph"><h1>Reporte Personalizado</h1><p class="sub">CU-19 — Elige la tabla y los campos que quieres incluir</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('reportes.index') }}">Reportes</a></li><li>Personalizado</li></ol></div>

{{-- Paso 1: tabla y gestión (recarga el formulario, NO genera) --}}
<form method="GET" action="{{ route('reportes.show','personalizado') }}" style="margin-bottom:1rem;display:flex;gap:.6rem;flex-wrap:wrap;align-items:center">
  <label style="font-size:.85rem;color:var(--t3,#8a8678)">Tabla:</label>
  <select name="tabla_sel" onchange="this.form.submit()" style="padding:.45rem .6rem;border:1px solid #d8d2c4;border-radius:6px;background:#fff">
    @foreach($catalogoCampos as $key => $def)
      <option value="{{ $key }}" {{ $key === $tabla ? 'selected':'' }}>{{ $def['titulo'] }}</option>
    @endforeach
  </select>
  <label style="font-size:.85rem;color:var(--t3,#8a8678)">Gestión:</label>
  <select name="gestion_id" onchange="this.form.submit()" style="padding:.45rem .6rem;border:1px solid #d8d2c4;border-radius:6px;background:#fff">
    @foreach($gestiones as $g)<option value="{{ $g->id }}" {{ $g->id==$gestionId?'selected':'' }}>{{ $g->descripcion }}</option>@endforeach
  </select>
</form>

{{-- Paso 2: campos a incluir y generación --}}
<form method="GET" action="{{ route('reportes.show','personalizado') }}">
  <input type="hidden" name="tabla" value="{{ $tabla }}">
  <input type="hidden" name="tabla_sel" value="{{ $tabla }}">
  <input type="hidden" name="gestion_id" value="{{ $gestionId }}">
  <div class="card"><div class="card-hd"><i class="fas fa-list-check"></i>Campos de «{{ $catalogoCampos[$tabla]['titulo'] }}»</div><div class="card-bd">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:.5rem .9rem">
      @foreach($catalogoCampos[$tabla]['campos'] as $key => [$etiqueta, $expr])
        <label style="display:flex;align-items:center;gap:.45rem;font-size:.88rem;cursor:pointer">
          <input type="checkbox" name="campos[]" value="{{ $key }}" {{ $loop->index < 4 ? 'checked':'' }}>
          {{ $etiqueta }}
        </label>
      @endforeach
    </div>
    <div style="margin-top:1rem;display:flex;gap:.5rem;align-items:center">
      <button type="submit" class="btn bp"><i class="fas fa-play"></i> Generar reporte</button>
      <span style="font-size:.78rem;color:var(--t3,#8a8678)">El reporte mostrará solo los campos marcados, en este orden; podrás exportarlo a PDF o Excel.</span>
    </div>
  </div></div>
</form>
@endsection
