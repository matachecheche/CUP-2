@extends('layouts.ap')
@section('title', 'Editar Usuario')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Usuario</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>

    <div class="card">
        <div class="card-header"><i class="fas fa-user-edit me-1"></i> Editar: {{ $user->name }}</div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nueva contraseña <small class="text-muted">(dejar vacío para no cambiar)</small></label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Rol *</label>
                        <select name="role" class="form-select" required>
                            <option value="">— Seleccionar —</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->name }}"
                                    {{ $user->hasRole($rol->name) ? 'selected' : '' }}>
                                    {{ $rol->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if($docentes->count())
                    <div class="col-md-6">
                        <label class="form-label">Docente vinculado</label>
                        <select name="docente_id" class="form-select">
                            <option value="">— Ninguno —</option>
                            @foreach($docentes as $d)
                                <option value="{{ $d->id }}" {{ $user->docente_id == $d->id ? 'selected' : '' }}>
                                    {{ $d->nombres }} {{ $d->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @if($postulantes->count())
                    <div class="col-md-6">
                        <label class="form-label">Postulante vinculado</label>
                        <select name="postulante_id" class="form-select">
                            <option value="">— Ninguno —</option>
                            @foreach($postulantes as $p)
                                <option value="{{ $p->id }}" {{ $user->postulante_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nombres }} {{ $p->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
