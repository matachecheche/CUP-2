@extends('layouts.ap')
@section('title', 'Crear Usuario')

@section('content')
<div class="page-header">
    <h1>Crear Usuario</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('panel') }}">Inicio</a></li>
        <li><a href="{{ route('users.index') }}">Usuarios</a></li>
        <li>Crear</li>
    </ol>
</div>

<div class="card" style="max-width:720px;">
    <div class="card-header"><i class="fas fa-user-plus"></i> Nuevo usuario</div>
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-row cols-2">
                <div>
                    <label class="form-label">Nombre completo *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label class="form-label">Correo electrónico *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label class="form-label">Contraseña *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Confirmar contraseña *</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Rol *</label>
                    <select name="role" class="form-select" required>
                        <option value="">— Seleccionar —</option>
                        @foreach($roles as $rol)
                        <option value="{{ $rol->name }}" {{ old('role') == $rol->name ? 'selected' : '' }}>
                            {{ $rol->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:flex; gap:.75rem; margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
