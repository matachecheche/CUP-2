@extends('layouts.ap')
@section('title','Carreras y Cupos')
@push('css')<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">@endpush
@section('content')
<div class="ph"><h1>Carreras y Cupos</h1><p class="sub">CU-07 / CU-08 — Las 4 carreras de la FICCT</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li>Carreras</li></ol></div>
@can('crear carreras')<div style="margin-bottom:1rem"><a href="{{ route('carreras.create') }}" class="btn bp"><i class="fas fa-plus"></i> Nueva Carrera</a></div>@endcan
<div class="card"><div class="card-hd"><i class="fas fa-graduation-cap"></i>Carreras de la Facultad FICCT</div><div class="card-bd">
<div class="tw"><table id="tc" class="ct" style="width:100%">
<thead><tr><th>#</th><th>Carrera</th><th>Sigla</th><th>Descripción</th><th>Estado</th><th>Acciones</th></tr></thead>
<tbody>@foreach($carreras as $c)<tr>
<td style="color:var(--t3);font-size:.8rem">{{ $loop->iteration }}</td>
<td><strong>{{ $c->nombre }}</strong></td>
<td><span class="bg baz">{{ $c->sigla??'—' }}</span></td>
<td style="font-size:.85rem;color:var(--t3)">{{ Str::limit($c->descripcion??'',55) }}</td>
<td><span class="bg {{ $c->estado?'bv':'bg2' }}">{{ $c->estado?'Activa':'Inactiva' }}</span></td>
<td><div class="bg3">
<a href="{{ route('carreras.show',$c) }}" class="btn bsm bo2" title="Ver / Cupos"><i class="fas fa-eye"></i></a>
@can('editar carreras')<a href="{{ route('carreras.edit',$c) }}" class="btn bsm bw"><i class="fas fa-edit"></i></a>@endcan
@can('eliminar carreras')<form action="{{ route('carreras.destroy',$c) }}" method="POST" style="display:inline">@csrf @method('DELETE')
<button class="btn bsm bdr" onclick="return confirm('¿Eliminar {{ $c->nombre }}?')"><i class="fas fa-trash"></i></button></form>@endcan
</div></td></tr>@endforeach</tbody></table></div></div></div>
@push('js')<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script>$(()=>$('#tc').DataTable({language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'},pageLength:10}))</script>@endpush
@endsection
