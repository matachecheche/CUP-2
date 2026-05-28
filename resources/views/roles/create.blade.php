@extends('layouts.ap')
@section('title', 'Crear Rol')
@section('content')
<div class="ph">
  <h1>Crear Rol</h1>
  <ol class="bc">
    <li><a href="{{ route('panel') }}">Inicio</a></li>
    <li><a href="{{ route('roles.index') }}">Roles</a></li>
    <li>Crear</li>
  </ol>
</div>

@if($errors->any())
<div class="al al-d"><i class="fas fa-exclamation-triangle"></i> Hay errores en el formulario. Revisa los campos marcados en rojo.</div>
@endif

<div class="card">
  <div class="card-hd"><i class="fas fa-shield-alt"></i> Nuevo rol</div>
  <div class="card-bd">
    <form action="{{ route('roles.store') }}" method="POST" novalidate>
      @csrf
      <div style="max-width:400px; margin-bottom:1.5rem;">
        <label class="fl">Nombre del rol <span class="rq">*</span></label>
        <input type="text" name="name" class="fc @error('name') is-invalid @enderror"
               value="{{ old('name') }}" required minlength="3" maxlength="80">
        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>

      <label class="fl">Permisos <span class="rq">*</span> <span style="color:var(--t3); font-weight:400;">(selecciona al menos uno)</span></label>
      @error('permission')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

      @foreach($permisos as $modulo => $lista)
      <div class="perm-group">
        <div class="perm-group-header">
          <span>{{ $modulo }}</span>
          <button type="button" class="btn bo2 bsm"
                  onclick="toggleGrupo('g_{{ Str::slug($modulo) }}')">Sel / Des</button>
        </div>
        <div class="perm-group-body" id="g_{{ Str::slug($modulo) }}">
          @foreach($lista as $permiso)
          <label class="fck">
            <input type="checkbox" name="permission[]" value="{{ $permiso->id }}"
                   {{ in_array($permiso->id, old('permission', [])) ? 'checked' : '' }}>
            <span style="font-size:.83rem;">{{ $permiso->name }}</span>
          </label>
          @endforeach
        </div>
      </div>
      @endforeach

      <div style="display:flex; gap:.75rem; margin-top:1.5rem;">
        <button type="submit" class="btn bp"><i class="fas fa-save"></i> Guardar</button>
        <a href="{{ route('roles.index') }}" class="btn bo2">Cancelar</a>
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
