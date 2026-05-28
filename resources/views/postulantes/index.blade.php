@extends('layouts.ap')
@section('title','Postulantes')
@push('css')<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">@endpush
@section('content')
<div class="ph"><h1>Registro de Postulantes</h1><p class="sub">CU-05 — Gestión de postulantes al Curso Preuniversitario (CUP)</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li>Postulantes</li></ol></div>
@can('crear postulantes')<div style="margin-bottom:1rem"><a href="{{ route('postulantes.create') }}" class="btn bp"><i class="fas fa-user-plus"></i> Registrar Postulante</a></div>@endcan
<div class="card"><div class="card-hd"><i class="fas fa-users"></i>Postulantes inscritos</div><div class="card-bd">
<div class="tw"><table id="tp" class="ct" style="width:100%">
<thead><tr><th>#</th><th>CI</th><th>Apellidos y Nombres</th><th>1ª Opción</th><th>2ª Opción</th><th>Documentos</th><th>Estado</th><th>Acciones</th></tr></thead>
<tbody>@foreach($postulantes as $p)<tr>
<td style="color:var(--t3);font-size:.8rem">{{ $loop->iteration }}</td>
<td style="font-family:'Courier New',monospace;font-size:.84rem">{{ $p->ci }}</td>
<td><strong>{{ $p->apellidos }}</strong>, {{ $p->nombres }}</td>
<td style="font-size:.84rem">{{ $p->primeraOpcion?->nombre??'—' }}</td>
<td style="font-size:.84rem">{{ $p->segundaOpcion?->nombre??'—' }}</td>
<td style="text-align:center">
@php $dc=($p->doc_ci?1:0)+($p->doc_libreta_colegio?1:0)+($p->doc_titulo_bachiller?1:0);@endphp
<span class="bg {{ $dc==3?'bv':($dc>0?'bna':'bd') }}">{{ $dc }}/3</span>
</td>
<td><span class="bg {{ $p->estado_badge }}">{{ ucfirst(str_replace('_',' ',$p->estado)) }}</span></td>
<td><div class="bg3">
<a href="{{ route('postulantes.show',$p) }}" class="btn bsm bo2"><i class="fas fa-eye"></i></a>
@can('editar postulantes')<a href="{{ route('postulantes.edit',$p) }}" class="btn bsm bw"><i class="fas fa-edit"></i></a>@endcan
@can('eliminar postulantes')<form action="{{ route('postulantes.destroy',$p) }}" method="POST" style="display:inline">@csrf @method('DELETE')
<button class="btn bsm bdr" onclick="return confirm('¿Eliminar a {{ $p->nombre_completo }}?')"><i class="fas fa-trash"></i></button></form>@endcan
</div></td></tr>@endforeach</tbody></table></div></div></div>
@push('js')<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script>$(()=>$('#tp').DataTable({language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'},order:[[2,'asc']],pageLength:20}))</script>@endpush
@endsection
