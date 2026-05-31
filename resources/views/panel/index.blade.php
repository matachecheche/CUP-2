@extends('layouts.ap')
@section('title','Panel de Control')
@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
@endpush
@section('content')
<div class="ph">
  <h1>Panel de Control</h1>
  <p class="sub">Sistema de Admisión — Curso Preuniversitario (CUP) · FICCT</p>
  <ol class="bc"><li>Inicio</li></ol>
</div>

<div class="sg">
  @can('ver postulantes')
  <a class="sc" href="{{ route('postulantes.index') }}">
    <div class="si c1"><i class="fas fa-users"></i></div>
    <div><div class="sv">{{ \App\Models\Postulante::count() }}</div><div class="sl">Postulantes inscritos</div></div>
  </a>
  @endcan
  @can('ver carreras')
  <a class="sc" href="{{ route('carreras.index') }}">
    <div class="si c2"><i class="fas fa-graduation-cap"></i></div>
    <div><div class="sv">{{ \App\Models\Carrera::count() }}</div><div class="sl">Carreras</div></div>
  </a>
  @endcan
  @can('ver docentes')
  <a class="sc" href="{{ route('docentes.index') }}">
    <div class="si c5"><i class="fas fa-chalkboard-teacher"></i></div>
    <div><div class="sv">{{ \App\Models\Docente::count() }}</div><div class="sl">Docentes</div></div>
  </a>
  @endcan
  @can('ver materias')
  <a class="sc" href="{{ route('materias.index') }}">
    <div class="si c6"><i class="fas fa-book-open"></i></div>
    <div><div class="sv">{{ \App\Models\Materia::count() }}</div><div class="sl">Materias</div></div>
  </a>
  @endcan
  @can('ver usuarios')
  <a class="sc" href="{{ route('users.index') }}">
    <div class="si c4"><i class="fas fa-users-cog"></i></div>
    <div><div class="sv">{{ \App\Models\User::count() }}</div><div class="sl">Usuarios del sistema</div></div>
  </a>
  @endcan
</div>

<div class="mg">
  {{-- M1: Autenticación y Seguridad --}}
  <div class="mc m1">
    <div class="mh"><div class="mn2">1</div>Módulo de Autenticación y Seguridad</div>
    <div class="mb2">
      <div class="cr2x lnk"><a href="{{ route('login') }}"><span class="ctg dn">CU-01</span><i class="ci2 fas fa-sign-in-alt"></i>Iniciar sesión</a></div>
      <div class="cr2x lnk"><a href="{{ route('logout') }}"><span class="ctg dn">CU-02</span><i class="ci2 fas fa-sign-out-alt"></i>Cerrar sesión</a></div>
      <div class="cr2x lnk"><a href="{{ route('password.request') }}"><span class="ctg dn">CU-03</span><i class="ci2 fas fa-key"></i>Recuperar contraseña</a></div>
      @can('ver usuarios')
      <div class="cr2x lnk"><a href="{{ route('users.index') }}"><span class="ctg dn">CU-04</span><i class="ci2 fas fa-users-cog"></i>Gestionar usuarios y roles</a></div>
      @else
      <div class="cr2x dis"><span class="ctg pn">CU-04</span><i class="ci2 fas fa-lock"></i>Gestionar usuarios y roles<span class="cpl">Sin acceso</span></div>
      @endcan
    </div>
  </div>

  {{-- M2: Registro de Postulantes y Gestión Académica (fusión visual) --}}
  <div class="mc m2">
    <div class="mh"><div class="mn2">2</div>Módulo de Registro de Postulantes y Gestión Académica</div>
    <div class="mb2">
      @can('ver postulantes')
      <div class="cr2x lnk"><a href="{{ route('postulantes.index') }}"><span class="ctg dn">CU-05</span><i class="ci2 fas fa-user-plus"></i>Gestionar postulantes</a></div>
      @else
      <div class="cr2x dis"><span class="ctg pn">CU-05</span><i class="ci2 fas fa-lock"></i>Gestionar postulantes<span class="cpl">Sin acceso</span></div>
      @endcan
      @can('ver gestiones')
      <div class="cr2x lnk"><a href="{{ route('gestiones.index') }}"><span class="ctg dn">CU-06</span><i class="ci2 fas fa-calendar-alt"></i>Gestionar gestiones académicas</a></div>
      @else
      <div class="cr2x dis"><span class="ctg pn">CU-06</span><i class="ci2 fas fa-lock"></i>Gestionar gestiones académicas<span class="cpl">Sin acceso</span></div>
      @endcan
      @can('ver carreras')
      <div class="cr2x lnk"><a href="{{ route('carreras.index') }}"><span class="ctg dn">CU-07</span><i class="ci2 fas fa-graduation-cap"></i>Gestionar carreras de la facultad</a></div>
      @else
      <div class="cr2x dis"><span class="ctg pn">CU-07</span><i class="ci2 fas fa-lock"></i>Gestionar carreras de la facultad<span class="cpl">Sin acceso</span></div>
      @endcan
      @can('ver cupos')
      <div class="cr2x lnk"><a href="{{ route('cupos.index') }}"><span class="ctg dn">CU-08</span><i class="ci2 fas fa-sliders-h"></i>Definir cupos por carrera y gestión</a></div>
      @else
      <div class="cr2x dis"><span class="ctg pn">CU-08</span><i class="ci2 fas fa-lock"></i>Definir cupos por carrera y gestión<span class="cpl">Sin acceso</span></div>
      @endcan
      @can('ver materias')
      <div class="cr2x lnk"><a href="{{ route('materias.index') }}"><span class="ctg dn">CU-09</span><i class="ci2 fas fa-book-open"></i>Gestionar materias del CUP</a></div>
      @else
      <div class="cr2x dis"><span class="ctg pn">CU-09</span><i class="ci2 fas fa-lock"></i>Gestionar materias del CUP<span class="cpl">Sin acceso</span></div>
      @endcan
      <div class="cr2x dis"><span class="ctg pn">CU-20</span><i class="ci2 fas fa-credit-card"></i>Gestionar pasarela de pago<span class="cpl">Próximamente</span></div>
    </div>
  </div>

  {{-- M3: Asignación de Grupos y Docentes --}}
  <div class="mc m4">
    <div class="mh"><div class="mn2">3</div>Módulo de Asignación de Grupos y Docentes</div>
    <div class="mb2">
      @can('ver docentes')
      <div class="cr2x lnk"><a href="{{ route('docentes.index') }}"><span class="ctg dn">CU-10</span><i class="ci2 fas fa-chalkboard-teacher"></i>Gestionar docentes</a></div>
      @else
      <div class="cr2x dis"><span class="ctg pn">CU-10</span><i class="ci2 fas fa-lock"></i>Gestionar docentes<span class="cpl">Sin acceso</span></div>
      @endcan
      @can('ver grupos')
      <div class="cr2x lnk"><a href="{{ route('grupos.index') }}"><span class="ctg dn">CU-11</span><i class="ci2 fas fa-layer-group"></i>Gestionar grupos</a></div>
      <div class="cr2x lnk"><a href="{{ route('grupos.index') }}"><span class="ctg dn">CU-12</span><i class="ci2 fas fa-user-tie"></i>Asignar docente a grupos y materias</a></div>
      @else
      <div class="cr2x dis"><span class="ctg pn">CU-11</span><i class="ci2 fas fa-lock"></i>Gestionar grupos<span class="cpl">Sin acceso</span></div>
      <div class="cr2x dis"><span class="ctg pn">CU-12</span><i class="ci2 fas fa-lock"></i>Asignar docente a grupos y materias<span class="cpl">Sin acceso</span></div>
      @endcan
    </div>
  </div>

  {{-- M4: Exámenes y Control Académico --}}
  <div class="mc m5">
    <div class="mh"><div class="mn2">4</div>Módulo de Exámenes y Control Académico</div>
    <div class="mb2">
      @can('ver notas')
      <div class="cr2x lnk"><a href="{{ route('notas.index') }}"><span class="ctg dn">CU-13</span><i class="ci2 fas fa-pencil-alt"></i>Registrar notas de exámenes</a></div>
      <div class="cr2x lnk"><a href="{{ route('notas.index') }}"><span class="ctg dn">CU-14</span><i class="ci2 fas fa-calculator"></i>Calcular nota final, promedio y estado</a></div>
      <div class="cr2x lnk"><a href="{{ route('notas.index') }}"><span class="ctg dn">CU-15</span><i class="ci2 fas fa-search"></i>Consultar notas del postulante</a></div>
      @else
      <div class="cr2x dis"><span class="ctg pn">CU-13</span><i class="ci2 fas fa-lock"></i>Registrar notas de exámenes<span class="cpl">Sin acceso</span></div>
      <div class="cr2x dis"><span class="ctg pn">CU-14</span><i class="ci2 fas fa-lock"></i>Calcular nota final, promedio y estado<span class="cpl">Sin acceso</span></div>
      <div class="cr2x dis"><span class="ctg pn">CU-15</span><i class="ci2 fas fa-lock"></i>Consultar notas del postulante<span class="cpl">Sin acceso</span></div>
      @endcan
    </div>
  </div>

  {{-- M5: Panel Administrativo y Reportes --}}
  <div class="mc m1" style="border-top-color:#10b981">
    <div class="mh"><div class="mn2" style="background:#d1fae5;color:#065f46">5</div>Módulo de Panel Administrativo y Reportes</div>
    <div class="mb2">
      @can('procesar admision')
      <div class="cr2x lnk"><a href="{{ route('admision.index') }}"><span class="ctg dn">CU-16</span><i class="ci2 fas fa-cogs"></i>Procesar admisión por primera opción</a></div>
      <div class="cr2x lnk"><a href="{{ route('admision.index') }}"><span class="ctg dn">CU-17</span><i class="ci2 fas fa-exchange-alt"></i>Reasignar postulantes a segunda opción</a></div>
      @else
      <div class="cr2x dis"><span class="ctg pn">CU-16</span><i class="ci2 fas fa-lock"></i>Procesar admisión por primera opción<span class="cpl">Sin acceso</span></div>
      <div class="cr2x dis"><span class="ctg pn">CU-17</span><i class="ci2 fas fa-lock"></i>Reasignar postulantes a segunda opción<span class="cpl">Sin acceso</span></div>
      @endcan
      @can('publicar admision')
      <div class="cr2x lnk"><a href="{{ route('admision.index') }}"><span class="ctg dn">CU-18</span><i class="ci2 fas fa-bullhorn"></i>Publicar resultado final de admisión</a></div>
      @else
      <div class="cr2x dis"><span class="ctg pn">CU-18</span><i class="ci2 fas fa-lock"></i>Publicar resultado final de admisión<span class="cpl">Sin acceso</span></div>
      @endcan
      <div class="cr2x dis"><span class="ctg pn">CU-19</span><i class="ci2 fas fa-chart-bar"></i>Gestionar reportes y estadísticas<span class="cpl">Próximamente</span></div>
    </div>
  </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
@endpush
@endsection
