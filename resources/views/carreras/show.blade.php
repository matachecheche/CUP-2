@extends('layouts.ap')
@section('title',$carrera->nombre)
@section('content')
<div class="ph"><h1>{{ $carrera->nombre }}</h1><p class="sub">CU-08 — Cupos por gestión académica</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('carreras.index') }}">Carreras</a></li><li>{{ $carrera->sigla }}</li></ol></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;max-width:780px;margin-bottom:1.5rem">
<div class="card"><div class="card-hd"><i class="fas fa-info-circle"></i>Información</div><div class="card-bd" style="font-size:.88rem">
<div style="margin-bottom:.6rem"><span style="color:var(--t3)">Nombre completo</span><div style="font-weight:600">{{ $carrera->nombre }}</div></div>
<div style="margin-bottom:.6rem"><span style="color:var(--t3)">Sigla</span><div><span class="bg baz">{{ $carrera->sigla??'—' }}</span></div></div>
<div><span style="color:var(--t3)">Estado</span><div><span class="bg {{ $carrera->estado?'bv':'bg2' }}">{{ $carrera->estado?'Activa':'Inactiva' }}</span></div></div>
</div></div>
<div class="card"><div class="card-hd"><i class="fas fa-sliders-h"></i>Definir cupo (CU-08)</div><div class="card-bd">
<form action="{{ route('carreras.cupos',$carrera) }}" method="POST">@csrf
<div style="margin-bottom:.75rem"><label class="fl">Gestión <span class="rq">*</span></label>
<select name="gestion_id" class="fs" required><option value="">— Seleccionar —</option>
@foreach($gestiones as $g)<option value="{{ $g->id }}">{{ $g->descripcion }}</option>@endforeach</select></div>
<div style="margin-bottom:.75rem"><label class="fl">Cupo máximo <span class="rq">*</span></label>
<input type="number" name="cantidad_maxima" class="fc" min="1" max="9999" required placeholder="Ej: 50"></div>
<button type="submit" class="btn bp bsm"><i class="fas fa-save"></i> Guardar cupo</button>
</form></div></div></div>
<div class="card" style="max-width:780px"><div class="card-hd"><i class="fas fa-table"></i>Cupos por gestión</div><div class="card-bd">
@if($cupos->isEmpty())<p style="color:var(--t3);font-size:.88rem;text-align:center;padding:1rem">Sin cupos definidos aún.</p>
@else<table class="ct"><thead><tr><th>Gestión</th><th>Cupo máximo</th><th>Registrado</th></tr></thead>
<tbody>@foreach($cupos as $q)<tr><td>{{ $q->gestion?->descripcion }}</td><td><strong style="color:var(--v)">{{ $q->cantidad_maxima }}</strong></td>
<td style="font-size:.8rem;color:var(--t3)">{{ $q->created_at->format('d/m/Y') }}</td></tr>@endforeach</tbody></table>@endif
</div></div>
<div style="margin-top:1rem;display:flex;gap:.75rem">
@can('editar carreras')<a href="{{ route('carreras.edit',$carrera) }}" class="btn bw"><i class="fas fa-edit"></i> Editar</a>@endcan
<a href="{{ route('carreras.index') }}" class="btn bo2"><i class="fas fa-arrow-left"></i> Volver</a></div>
@endsection
