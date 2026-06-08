@extends('layouts.ap')
@section('title','Nueva Asignación')
@section('content')
<div class="ph"><h1>Asignar Docente</h1><p class="sub">CU-12 · Vincular docente ↔ grupo ↔ materia con horario</p>
<ol class="bc"><li><a href="{{ route('panel') }}">Inicio</a></li><li><a href="{{ route('asignaciones.index') }}">Asignaciones</a></li><li>Nueva</li></ol></div>
<form action="{{ route('asignaciones.store') }}" method="POST" novalidate>@csrf
  @include('asignaciones._form')
</form>
@endsection
