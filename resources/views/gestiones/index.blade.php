@extends('layouts.ap')
@section('title','Gestiones Académicas')
@push('css')<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">@endpush
@section('content')
<div class="ph"><h1>Gestiones Académicas</h1><p class="sub">CU-06 — Periodos del Curso Preuniversitario</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li>Gestiones</li></ol></div>
@can('crear gestiones')<div style="margin-bottom:1rem"><a href="{{ route('gestiones.create') }}" class="btn bp"><i class="fas fa-plus"></i> Nueva Gestión</a></div>@endcan
<div class="card"><div class="card-hd"><i class="fas fa-calendar-alt"></i>Gestiones registradas</div><div class="card-bd">
<div class="tw"><table id="tg" class="ct" style="width:100%">
<thead><tr><th>#</th><th>Descripción</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Estado</th><th>Acciones</th></tr></thead>
<tbody>@foreach($gestiones as $g)<tr>
<td style="color:var(--t3);font-size:.8rem">{{ $loop->iteration }}</td>
<td><strong>{{ $g->descripcion }}</strong></td>
<td>{{ $g->fecha_inicio->format('d/m/Y') }}</td>
<td>{{ $g->fecha_fin->format('d/m/Y') }}</td>
<td>@php $ec=['planificacion'=>'baz','inscripcion'=>'bna','en_curso'=>'bv','finalizado'=>'bg2']@endphp
<span class="bg {{ $ec[$g->estado]??'bg2' }}">{{ ucfirst(str_replace('_',' ',$g->estado)) }}</span></td>
<td><div class="bg3">
<a href="{{ route('gestiones.edit',$g) }}" class="btn bsm bw"><i class="fas fa-edit"></i></a>
@can('eliminar gestiones')<form action="{{ route('gestiones.destroy',$g) }}" method="POST" style="display:inline">@csrf @method('DELETE')
<button class="btn bsm bdr" onclick="return confirm('¿Eliminar?')"><i class="fas fa-trash"></i></button></form>@endcan
</div></td></tr>@endforeach</tbody></table></div></div></div>
@push('js')<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script>$(()=>$('#tg').DataTable({language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'},pageLength:10}))</script>@endpush
@endsection
