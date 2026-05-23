@extends('layouts.ap')
@section('title', 'Editar Rol')

@section('content')
<div class="page-header">
    <h1>Editar Rol: {{ $role->name }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('panel') }}">Inicio</a></li>
        <li><a href="{{ route('roles.index') }}">Roles</a></li>
        <li>Editar</li>
    </ol>
</div>

<div class="card">
    <div class="card-header"><i class="fas fa-edit"></i> Editando: {{ $role->name }}</div>
    <div class="card-body">
        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf @method('PUT')
            <div style="max-width:400px; margin-bottom:1.5rem;">
                <label class="form-label">Nombre del rol *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
            </div>

            <label class="form-label">Permisos *</label>
            @foreach($permisos as $modulo => $lista)
            <div class="perm-group">
                <div class="perm-group-header">
                    <span>{{ $modulo }}</span>
                    <button type="button" class="btn btn-xs btn-outline"
                        onclick="toggleGrupo('g_{{ Str::slug($modulo) }}')">
                        Sel / Des
                    </button>
                </div>
                <div class="perm-group-body" id="g_{{ Str::slug($modulo) }}">
                    @foreach($lista as $permiso)
                    <label class="form-check">
                        <input type="checkbox" name="permission[]"
                            value="{{ $permiso->id }}"
                            {{ in_array($permiso->name, $permisosRol) ? 'checked' : '' }}>
                        <span style="font-size:.83rem;">{{ $permiso->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div style="display:flex; gap:.75rem; margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
                <a href="{{ route('roles.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@push('js')
<script>
function toggleGrupo(id) {
    const checks = document.querySelectorAll('#' + id + ' input[type=checkbox]');
    const all = Array.from(checks).every(c => c.checked);
    checks.forEach(c => c.checked = !all);
}
</script>
@endpush
@endsection
