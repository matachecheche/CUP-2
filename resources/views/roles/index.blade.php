@extends('layouts.ap')
@section('title', 'Roles y Permisos')

@section('content')
<div class="page-header">
    <h1>Roles y Permisos</h1>
    <p class="subtitle">Configuración de accesos por tipo de usuario</p>
    <ol class="breadcrumb">
        <li><a href="{{ route('panel') }}">Inicio</a></li>
        <li>Roles</li>
    </ol>
</div>

@can('crear roles')
<div style="margin-bottom:1rem;">
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Rol
    </a>
</div>
@endcan

<div class="card">
    <div class="card-header"><i class="fas fa-user-shield"></i> Roles del sistema</div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="cup-table">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Permisos asignados</th>
                        <th>Vista previa</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php $protegidos = ['Administrador del Sistema', 'Docente', 'Postulante']; @endphp
                    @foreach($roles as $rol)
                    <tr>
                        <td>
                            <strong>{{ $rol->name }}</strong>
                            @if(in_array($rol->name, $protegidos))
                            <span class="badge badge-oro" style="margin-left:.4rem; font-size:.65rem;">base</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-verde">{{ $rol->permissions->count() }} permisos</span>
                        </td>
                        <td>
                            @foreach($rol->permissions->take(3) as $p)
                            <span class="badge badge-gris" style="margin:.1rem;">{{ $p->name }}</span>
                            @endforeach
                            @if($rol->permissions->count() > 3)
                            <span style="font-size:.75rem; color:var(--txt-3);">+{{ $rol->permissions->count()-3 }} más</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:.4rem;">
                                @can('editar roles')
                                <a href="{{ route('roles.edit', $rol) }}" class="btn btn-sm btn-outline" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('eliminar roles')
                                @if(!in_array($rol->name, $protegidos))
                                <form action="{{ route('roles.destroy', $rol) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar el rol {{ $rol->name }}?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
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
@endsection
