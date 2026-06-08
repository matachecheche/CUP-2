@extends('layouts.ap')
@section('title','Editar Asignación')
@section('content')
<div class="ph"><h1>Editar Asignación</h1><p class="sub">CU-12 · Modificar el vínculo docente ↔ grupo ↔ materia</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('asignaciones.index') }}">Asignaciones</a></li><li>Editar</li></ol></div>
<form action="{{ route('asignaciones.update',$asignacion) }}" method="POST" novalidate>@csrf @method('PUT')
  @include('asignaciones._form')
</form>
@endsection
