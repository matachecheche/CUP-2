@extends('layouts.ap')
@section('title','Docentes')
@push('css')<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">@endpush
@section('content')
<div class="ph"><h1>Docentes</h1><p class="sub">CU-10 — Gestión de docentes (máximo 4 grupos por docente)</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li>Docentes</li></ol></div>
@can('crear docentes')<div style="margin-bottom:1rem"><a href="{{ route('docentes.create') }}" class="btn bp"><i class="fas fa-user-plus"></i> Registrar Docente</a></div>@endcan
<div class="card"><div class="card-hd"><i class="fas fa-chalkboard-teacher"></i>Docentes contratados para el CUP</div><div class="card-bd">
<div class="tw"><table id="td" class="ct" style="width:100%">
<thead><tr><th>#</th><th>CI</th><th>Apellidos y Nombres</th><th>Área</th><th>Título</th><th>Maestría</th><th>Perfil OK</th><th>Estado</th><th>Acciones</th></tr></thead>
<tbody>@foreach($docentes as $d)<tr>
<td style="color:var(--t3);font-size:.8rem">{{ $loop->iteration }}</td>
<td style="font-family:'Courier New',monospace;font-size:.84rem">{{ $d->ci }}</td>
<td><strong>{{ $d->apellidos }}</strong>, {{ $d->nombres }}</td>
<td style="font-size:.83rem">{{ $d->area_formacion??'—' }}</td>
<td style="font-size:.81rem;color:var(--t3)">{{ Str::limit($d->titulo_profesional??'',35) }}</td>
<td style="font-size:.81rem;color:var(--t3)">{{ $d->maestria ? '✓ '.Str::limit($d->maestria,25) : '—' }}</td>
<td style="text-align:center">
@php $ok = $d->titulo_profesional && $d->maestria && $d->diplomado_educacion_superior; @endphp
<span class="bg {{ $ok ? 'bv':'bd' }}" title="{{ $ok ? 'Perfil completo':'Falta título, maestría o diplomado' }}">
  {{ $ok ? '✔ Válido':'✘ Incompleto' }}
</span>
</td>
<td><span class="bg {{ $d->estado?'bv':'bg2' }}">{{ $d->estado?'Activo':'Inactivo' }}</span></td>
<td><div class="bg3">
<a href="{{ route('docentes.show',$d) }}" class="btn bsm bo2"><i class="fas fa-eye"></i></a>
@can('editar docentes')<a href="{{ route('docentes.edit',$d) }}" class="btn bsm bw"><i class="fas fa-edit"></i></a>@endcan
@can('eliminar docentes')<form action="{{ route('docentes.destroy',$d) }}" method="POST" style="display:inline">@csrf @method('DELETE')
<button class="btn bsm bdr" onclick="return confirm('¿Desactivar?')"><i class="fas fa-ban"></i></button></form>@endcan
</div></td></tr>@endforeach</tbody></table></div></div></div>
@push('js')<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script>$(()=>$('#td').DataTable({language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'},order:[[2,'asc']],pageLength:15}))</script>@endpush
@endsection
