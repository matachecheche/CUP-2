@extends('layouts.ap')
@section('title','Estudiantes del grupo')
@section('content')
<div class="ph"><h1>Estudiantes — {{ $grupo->codigo }}</h1>
<p class="sub">CU-11 · {{ ucfirst($grupo->turno) }} · {{ ucfirst($grupo->modalidad) }} · {{ $estudiantes->count() }}/{{ $grupo->capacidad_maxima }} inscritos · {{ $grupo->gestion?->descripcion }}</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('grupos.index') }}">Grupos</a></li><li><a href="{{ route('grupos.show',$grupo) }}">{{ $grupo->codigo }}</a></li><li>Estudiantes</li></ol></div>

<div class="card"><div class="card-hd"><i class="fas fa-users"></i>Inscritos en {{ $grupo->codigo }}</div><div class="card-bd">
@if($estudiantes->isEmpty())
  <p style="color:var(--t3);text-align:center;padding:.75rem">Este grupo aún no tiene estudiantes inscritos.</p>
@else
<table class="ct" style="width:100%">
<thead><tr><th>#</th><th>CI</th><th>Apellidos y Nombres</th><th>1ª Opción</th><th>Estado</th><th>Promedio</th></tr></thead>
<tbody>@foreach($estudiantes as $p)<tr>
<td style="color:var(--t3);font-size:.8rem">{{ $loop->iteration }}</td>
<td style="font-family:'Courier New',monospace;font-size:.84rem">{{ $p->ci }}</td>
<td><strong>{{ $p->apellidos }}</strong>, {{ $p->nombres }}</td>
<td style="font-size:.84rem">{{ $p->primeraOpcion?->nombre ?? '—' }}</td>
<td><span class="bg {{ in_array($p->estado,['aprobado','admitido','admitido_segunda_opcion'])?'bv':($p->estado==='en_curso'?'bna':'bg2') }}">{{ ucfirst(str_replace('_',' ',$p->estado)) }}</span></td>
<td>{{ $p->promedio_general ?? '—' }}</td>
</tr>@endforeach</tbody></table>
@endif
</div></div>
<div style="margin-top:1rem"><a href="{{ route('grupos.show',$grupo) }}" class="btn bo2"><i class="fas fa-arrow-left"></i> Volver al grupo</a></div>
@endsection
