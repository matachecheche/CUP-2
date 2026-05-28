@extends('layouts.ap')
@section('title', 'Editar Usuario')
@section('content')
<div class="ph">
  <h1>Editar Usuario</h1>
  <ol class="bc">
    <li><a href="{{ route('panel') }}">Inicio</a></li>
    <li><a href="{{ route('users.index') }}">Usuarios</a></li>
    <li>Editar</li>
  </ol>
</div>

@if($errors->any())
<div class="al al-d"><i class="fas fa-exclamation-triangle"></i> Hay errores en el formulario. Revisa los campos marcados en rojo.</div>
@endif

<div class="card" style="max-width:720px;">
  <div class="card-hd"><i class="fas fa-user-edit"></i> Editando: {{ $user->name }}</div>
  <div class="card-bd">
    <form action="{{ route('users.update', $user) }}" method="POST" novalidate>
      @csrf @method('PUT')
      <div class="fr c2g">
        <div>
          <label class="fl">Nombre completo <span class="rq">*</span></label>
          <input type="text" name="name" class="fc @error('name') is-invalid @enderror"
                 value="{{ old('name', $user->name) }}" required maxlength="100"
                 pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s\.\-]+">
          @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="fl">Correo electrónico <span class="rq">*</span></label>
          <input type="email" name="email" class="fc @error('email') is-invalid @enderror"
                 value="{{ old('email', $user->email) }}" required maxlength="100">
          @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="fl">Nueva contraseña <span style="color:var(--t3); font-weight:400;">(opcional)</span></label>
          <input type="password" name="password" class="fc @error('password') is-invalid @enderror" minlength="8">
          @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="fl">Confirmar contraseña</label>
          <input type="password" name="password_confirmation" class="fc" minlength="8">
        </div>
        <div>
          <label class="fl">Rol <span class="rq">*</span></label>
          <select name="role" class="fs @error('role') is-invalid @enderror" required>
            <option value="">— Seleccionar —</option>
            @foreach($roles as $rol)
              <option value="{{ $rol->name }}" {{ $user->hasRole($rol->name) ? 'selected' : '' }}>{{ $rol->name }}</option>
            @endforeach
          </select>
          @error('role')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        @if($docentes->isNotEmpty())
        <div>
          <label class="fl">Docente vinculado</label>
          <select name="docente_id" class="fs @error('docente_id') is-invalid @enderror">
            <option value="">—</option>
            @foreach($docentes as $d)
              <option value="{{ $d->id }}" {{ (old('docente_id',$user->docente_id))==$d->id?'selected':'' }}>{{ $d->apellidos }}, {{ $d->nombres }}</option>
            @endforeach
          </select>
          @error('docente_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        @endif
        @if($postulantes->isNotEmpty())
        <div>
          <label class="fl">Postulante vinculado</label>
          <select name="postulante_id" class="fs @error('postulante_id') is-invalid @enderror">
            <option value="">—</option>
            @foreach($postulantes as $p)
              <option value="{{ $p->id }}" {{ (old('postulante_id',$user->postulante_id))==$p->id?'selected':'' }}>{{ $p->apellidos }}, {{ $p->nombres }}</option>
            @endforeach
          </select>
          @error('postulante_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        @endif
      </div>
      @error('vínculo')<div class="al al-d" style="margin-top:.75rem">{{ $message }}</div>@enderror
      <div style="display:flex; gap:.75rem; margin-top:1.5rem;">
        <button type="submit" class="btn bp"><i class="fas fa-save"></i> Actualizar</button>
        <a href="{{ route('users.index') }}" class="btn bo2">Cancelar</a>
      </div>
    </form>
  </div>
</div>
@endsection
