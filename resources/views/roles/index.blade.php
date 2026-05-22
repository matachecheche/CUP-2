@extends('layouts.ap')
@section('title', 'Roles')

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4">
    <h1 class="mt-4">Roles y Permisos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Roles</li>
    </ol>

    @can('crear roles')
    <div class="mb-3">
        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Nuevo Rol
        </a>
    </div>
    @endcan

    <div class="card">
        <div class="card-header"><i class="fas fa-shield-alt me-1"></i> Listado de Roles</div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped table-sm">
                <thead>
                    <tr><th>Rol</th><th>Permisos</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @foreach ($roles as $rol)
                    <tr>
                        <td><strong>{{ $rol->name }}</strong></td>
                        <td>
                            @foreach($rol->permissions->take(5) as $p)
                                <span class="badge bg-light text-dark border">{{ $p->name }}</span>
                            @endforeach
                            @if($rol->permissions->count() > 5)
                                <span class="text-muted small">+ {{ $rol->permissions->count() - 5 }} más</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @can('editar roles')
                                <a href="{{ route('roles.edit', $rol) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('eliminar roles')
                                @if($rol->name !== 'Administrador')
                                <form action="{{ route('roles.destroy', $rol) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar rol?')">
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
