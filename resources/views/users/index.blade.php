@extends('layouts.ap')
@section('title', 'Usuarios del Sistema')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="page-header">
    <h1>Gestión de Usuarios</h1>
    <p class="subtitle">Administración de cuentas de acceso al sistema</p>
    <ol class="breadcrumb">
        <li><a href="{{ route('panel') }}">Inicio</a></li>
        <li>Usuarios</li>
    </ol>
</div>

@can('crear usuarios')
<div style="margin-bottom:1rem;">
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Nuevo Usuario
    </a>
</div>
@endcan

<div class="card">
    <div class="card-header">
        <i class="fas fa-users-cog"></i> Usuarios registrados
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table id="tablaUsuarios" class="cup-table" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td style="color:var(--txt-3); font-size:.8rem;">{{ $loop->iteration }}</td>
                        <td style="font-weight:500;">{{ $user->name }}</td>
                        <td style="color:var(--txt-3); font-size:.87rem;">{{ $user->email }}</td>
                        <td>
                            @foreach($user->getRoleNames() as $rol)
                                @php
                                    $cls = match($rol) {
                                        'Administrador del Sistema' => 'rol-admin',
                                        'Docente'                   => 'rol-docente',
                                        'Postulante'                => 'rol-postulante',
                                        default                     => '',
                                    };
                                @endphp
                                <span class="badge {{ $cls }}">{{ $rol }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge {{ $user->activo ? 'badge-verde' : 'badge-gris' }}">
                                {{ $user->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:.4rem; flex-wrap:wrap;">
                                @can('editar usuarios')
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('eliminar usuarios')
                                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm {{ $user->activo ? 'btn-danger' : 'btn-primary' }}"
                                        title="{{ $user->activo ? 'Desactivar' : 'Activar' }}"
                                        onclick="return confirm('¿Confirmar cambio de estado?')">
                                        <i class="fas fa-{{ $user->activo ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(document).ready(function(){
    $('#tablaUsuarios').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 15,
        order: [[0,'asc']]
    });
});
</script>
@endpush
@endsection
