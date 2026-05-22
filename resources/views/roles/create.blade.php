@extends('layouts.ap')
@section('title', 'Crear Rol')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Crear Rol</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
        <li class="breadcrumb-item active">Crear</li>
    </ol>

    <div class="card">
        <div class="card-header"><i class="fas fa-shield-alt me-1"></i> Nuevo Rol</div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nombre del rol *</label>
                    <input type="text" name="name" class="form-control w-50" value="{{ old('name') }}" required>
                </div>

                <label class="form-label fw-bold">Permisos *</label>
                @foreach($permisos as $modulo => $lista)
                <div class="card mb-2">
                    <div class="card-header py-1 bg-light fw-semibold small">{{ $modulo }}</div>
                    <div class="card-body py-2">
                        <div class="row">
                            @foreach($lista as $permiso)
                            <div class="col-md-3 col-sm-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="permission[]" value="{{ $permiso->id }}"
                                           id="perm_{{ $permiso->id }}"
                                           {{ in_array($permiso->id, old('permission', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="perm_{{ $permiso->id }}">
                                        {{ $permiso->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
