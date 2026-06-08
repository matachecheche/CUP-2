@extends('layouts.ap')
@section('title','Asignaciones')
@section('content')
<div class="ph"><h1>Asignar Docente a Grupos y Materias</h1><p class="sub">CU-12 · Docente ↔ grupo ↔ materia con horario — {{ $gestion->descripcion ?? 'sin gestión activa' }}</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li>Asignaciones</li></ol></div>

@if(session('success'))<div class="al al-v" style="margin-bottom:1rem"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
@if(session('error'))<div class="al al-d" style="margin-bottom:1rem"><i class="fas fa-times-circle"></i> {{ session('error') }}</div>@endif

@can('crear grupos')
<div style="margin-bottom:1rem"><a href="{{ route('asignaciones.create') }}" class="btn bp"><i class="fas fa-user-plus"></i> Nueva asignación</a></div>
@endcan

<form method="GET" style="margin-bottom:1rem;display:flex;gap:.6rem;flex-wrap:wrap">
  <select name="grupo_id" onchange="this.form.submit()" style="padding:.45rem .6rem;border:1px solid #d8d2c4;border-radius:6px;background:#fff">
    <option value="">— Todos los grupos —</option>
    @foreach($grupos as $g)<option value="{{ $g->id }}" {{ (string)request('grupo_id')===(string)$g->id?'selected':'' }}>{{ $g->codigo }}</option>@endforeach
  </select>
  <select name="docente_id" onchange="this.form.submit()" style="padding:.45rem .6rem;border:1px solid #d8d2c4;border-radius:6px;background:#fff">
    <option value="">— Todos los docentes —</option>
    @foreach($docentes as $d)<option value="{{ $d->id }}" {{ (string)request('docente_id')===(string)$d->id?'selected':'' }}>{{ $d->apellidos }}, {{ $d->nombres }}</option>@endforeach
  </select>
  <select name="materia_id" onchange="this.form.submit()" style="padding:.45rem .6rem;border:1px solid #d8d2c4;border-radius:6px;background:#fff">
    <option value="">— Todas las materias —</option>
    @foreach($materias as $m)<option value="{{ $m->id }}" {{ (string)request('materia_id')===(string)$m->id?'selected':'' }}>{{ $m->nombre }}</option>@endforeach
  </select>
  @if(request()->hasAny(['grupo_id','docente_id','materia_id']))<a class="btn bsm bo2" href="{{ route('asignaciones.index') }}">Limpiar</a>@endif
</form>

<div class="card"><div class="card-hd"><i class="fas fa-user-tie"></i>Asignaciones ({{ $asignaciones->count() }})</div><div class="card-bd">
@if($asignaciones->isEmpty())
  <p style="color:var(--t3);text-align:center;padding:.75rem">No hay asignaciones que coincidan. Crea una con «Nueva asignación».</p>
@else
<table class="ct" style="width:100%">
<thead><tr><th>Grupo</th><th>Materia</th><th>Docente</th><th>Área</th><th>Día</th><th>Horario</th><th>Aula</th><th>Acciones</th></tr></thead>
<tbody>@foreach($asignaciones as $a)<tr>
<td><strong>{{ $a->grupo?->codigo }}</strong></td>
<td>{{ $a->materia?->nombre }}</td>
<td>{{ $a->docente?->apellidos }}, {{ $a->docente?->nombres }}</td>
<td style="font-size:.82rem;color:var(--t3)">{{ $a->docente?->area_formacion ?? '—' }}</td>
<td>{{ ucfirst($a->dia) }}</td>
<td style="font-size:.84rem">{{ substr($a->hora_inicio,0,5) }}–{{ substr($a->hora_fin,0,5) }}</td>
<td>{{ $a->aula ?? '—' }}</td>
<td><div class="bg3">
@can('editar grupos')<a href="{{ route('asignaciones.edit',$a) }}" class="btn bsm bw"><i class="fas fa-edit"></i></a>@endcan
@can('eliminar grupos')<form action="{{ route('asignaciones.destroy',$a) }}" method="POST" style="display:inline">@csrf @method('DELETE')
<button class="btn bsm bdr" onclick="return confirm('¿Eliminar esta asignación?')"><i class="fas fa-trash"></i></button></form>@endcan
</div></td></tr>@endforeach</tbody></table>
@endif
</div></div>
@endsection
