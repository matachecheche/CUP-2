@extends('layouts.ap')
@section('title','Materias del CUP')
@section('content')
<div class="ph"><h1>Materias del CUP</h1><p class="sub">CU-09 — Computación · Matemáticas · Física · Inglés — Ponderación 30%+30%+40%</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li>Materias</li></ol></div>
@can('crear materias')<div style="margin-bottom:1rem"><a href="{{ route('materias.create') }}" class="btn bp"><i class="fas fa-plus"></i> Nueva Materia</a></div>@endcan
<div class="card"><div class="card-hd"><i class="fas fa-book-open"></i>Materias del Curso Preuniversitario</div><div class="card-bd">
<div class="tw"><table class="ct">
<thead><tr><th>Ord</th><th>Materia</th><th>Área de Formación</th><th>Exam1</th><th>Exam2</th><th>Exam3</th><th>Nota Mín.</th><th>Estado</th><th>Acciones</th></tr></thead>
<tbody>@foreach($materias as $m)<tr>
<td style="color:var(--t3)">{{ $m->orden }}</td>
<td><strong>{{ $m->nombre }}</strong></td>
<td style="font-size:.84rem;color:var(--t3)">{{ $m->area_formacion??'—' }}</td>
<td style="text-align:center"><span class="bg baz">{{ $m->pond_examen1 }}%</span></td>
<td style="text-align:center"><span class="bg baz">{{ $m->pond_examen2 }}%</span></td>
<td style="text-align:center"><span class="bg bna">{{ $m->pond_examen3 }}%</span></td>
<td style="text-align:center;font-weight:600">{{ $m->nota_minima_aprobacion }}</td>
<td><span class="bg {{ $m->estado?'bv':'bg2' }}">{{ $m->estado?'Activa':'Inactiva' }}</span></td>
<td><div class="bg3">
@can('editar materias')<a href="{{ route('materias.edit',$m) }}" class="btn bsm bw"><i class="fas fa-edit"></i></a>@endcan
@can('eliminar materias')<form action="{{ route('materias.destroy',$m) }}" method="POST" style="display:inline">@csrf @method('DELETE')
<button class="btn bsm bdr" onclick="return confirm('¿Eliminar {{ $m->nombre }}?')"><i class="fas fa-trash"></i></button></form>@endcan
</div></td></tr>@endforeach</tbody></table></div></div></div>
@endsection
