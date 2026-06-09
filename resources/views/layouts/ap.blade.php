<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','CUP') — Admisión CUP</title>
<link href="{{ asset('css/cup.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@stack('css')
</head>
<body>
<header class="cup-top">
  <button class="tgl" id="sbTgl"><i class="fas fa-bars"></i></button>
  <a href="{{ route('panel') }}" class="brand">
    <div class="bico">C</div>
    <span>Admisión <span style="color:var(--o)">CUP</span></span>
  </a>
  <div class="top-r">
    <div class="top-usr" id="usrDd" onclick="this.classList.toggle('open')">
      <div class="av">{{ strtoupper(substr(Auth::user()->name??'U',0,1)) }}</div>
      <span style="display:none" class="d-sm">{{ Auth::user()->name??'Usuario' }}</span>
      <i class="fas fa-chevron-down" style="font-size:.6rem;opacity:.6;margin-left:.25rem"></i>
      <div class="umenu">
        <a href="{{ route('users.perfil') }}"><i class="fas fa-user-circle"></i> Mi perfil</a>
        <div class="sep"></div>
        <a href="{{ route('logout') }}" class="dng"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
      </div>
    </div>
  </div>
</header>

<nav class="cup-sb" id="cupSb">
  <div class="sb-usr">
    <div class="av">{{ strtoupper(substr(Auth::user()->name??'U',0,1)) }}</div>
    <div>
      <div class="sbn">{{ Auth::user()->name??'Usuario' }}</div>
      <div class="sbr">{{ Auth::user()->getRoleNames()->first()??'Sin rol' }}</div>
    </div>
  </div>

  {{-- Módulo 1: Autenticación y Seguridad --}}
  <div class="sb-sec">
    <div class="sb-ttl">🔐 Autenticación y Seguridad</div>
    <a class="ni {{ request()->routeIs('panel') ? 'act':'' }}" href="{{ route('panel') }}">
      <i class="ico fas fa-th-large"></i>Panel de Control</a>
    <a class="ni" href="{{ route('login') }}">
      <i class="ico fas fa-sign-in-alt"></i>CU-01 · Iniciar sesión</a>
    <a class="ni" href="{{ route('logout') }}">
      <i class="ico fas fa-sign-out-alt"></i>CU-02 · Cerrar sesión</a>
    <a class="ni" href="{{ route('password.request') }}">
      <i class="ico fas fa-key"></i>CU-03 · Recuperar contraseña</a>
    @can('ver usuarios')
    <a class="ni {{ request()->routeIs('users.*') ? 'act':'' }}" href="{{ route('users.index') }}">
      <i class="ico fas fa-users-cog"></i>CU-04 · Gestionar usuarios y roles</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-users-cog"></i>CU-04 · Gestionar usuarios y roles<span class="nbg">Sin acceso</span></span>
    @endcan
    @can('ver roles')
    <a class="ni {{ request()->routeIs('roles.*') ? 'act':'' }}" href="{{ route('roles.index') }}">
      <i class="ico fas fa-user-shield"></i>Roles y Permisos</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-user-shield"></i>Roles y Permisos<span class="nbg">Sin acceso</span></span>
    @endcan
    @can('ver bitacora')
    <a class="ni {{ request()->routeIs('bitacora.*') ? 'act':'' }}" href="{{ route('bitacora.index') }}">
      <i class="ico fas fa-journal-whills"></i>Bitácora</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-journal-whills"></i>Bitácora<span class="nbg">Sin acceso</span></span>
    @endcan
  </div>
  <div class="sbdiv"></div>

  {{-- Módulo 2: Registro de Postulantes y Gestión Académica --}}
  <div class="sb-sec">
    <div class="sb-ttl">👤 Registro de Postulantes y Gestión Académica</div>
    @can('ver postulantes')
    <a class="ni {{ request()->routeIs('postulantes.*') ? 'act':'' }}" href="{{ route('postulantes.index') }}">
      <i class="ico fas fa-user-plus"></i>CU-05 · Gestionar postulantes</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-user-plus"></i>CU-05 · Gestionar postulantes<span class="nbg">Sin acceso</span></span>
    @endcan
    @can('ver gestiones')
    <a class="ni {{ request()->routeIs('gestiones.*') ? 'act':'' }}" href="{{ route('gestiones.index') }}">
      <i class="ico fas fa-calendar-alt"></i>CU-06 · Gestionar gestiones académicas</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-calendar-alt"></i>CU-06 · Gestionar gestiones académicas<span class="nbg">Sin acceso</span></span>
    @endcan
    @can('ver carreras')
    <a class="ni {{ request()->routeIs('carreras.*') ? 'act':'' }}" href="{{ route('carreras.index') }}">
      <i class="ico fas fa-graduation-cap"></i>CU-07 · Gestionar carreras de la facultad</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-graduation-cap"></i>CU-07 · Gestionar carreras de la facultad<span class="nbg">Sin acceso</span></span>
    @endcan
    @can('ver cupos')
    <a class="ni {{ request()->routeIs('cupos.*') ? 'act':'' }}" href="{{ route('cupos.index') }}">
      <i class="ico fas fa-sliders-h"></i>CU-08 · Definir cupos por carrera y gestión</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-sliders-h"></i>CU-08 · Definir cupos por carrera y gestión<span class="nbg">Sin acceso</span></span>
    @endcan
    @can('ver materias')
    <a class="ni {{ request()->routeIs('materias.*') ? 'act':'' }}" href="{{ route('materias.index') }}">
      <i class="ico fas fa-book-open"></i>CU-09 · Gestionar materias del CUP</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-book-open"></i>CU-09 · Gestionar materias del CUP<span class="nbg">Sin acceso</span></span>
    @endcan
    @if(auth()->user()->postulante && auth()->user()->postulante->estado === 'preinscrito')
    <a class="ni {{ request()->routeIs('pagos.*') ? 'act':'' }}" href="{{ route('pagos.pagar', auth()->user()->postulante_id) }}">
      <i class="ico fas fa-credit-card"></i>CU-20 · Pagar mi inscripción<span class="nbg">Pendiente</span></a>
    @elseif(auth()->user()->postulante)
    <span class="ni pnd"><i class="ico fas fa-check-circle"></i>CU-20 · Pago de inscripción<span class="nbg">Realizado</span></span>
    @elseif(auth()->user()->can('ver postulantes'))
    <a class="ni {{ request()->routeIs('pagos.*') ? 'act':'' }}" href="{{ route('pagos.index') }}">
      <i class="ico fas fa-credit-card"></i>CU-20 · Gestionar pasarela de pago</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-credit-card"></i>CU-20 · Gestionar pasarela de pago<span class="nbg">Sin acceso</span></span>
    @endif
  </div>
  <div class="sbdiv"></div>

  {{-- Módulo 3: Asignación de Grupos y Docentes --}}
  <div class="sb-sec">
    <div class="sb-ttl">🏫 Asignación de Grupos y Docentes</div>
    @can('ver docentes')
    <a class="ni {{ request()->routeIs('docentes.*') ? 'act':'' }}" href="{{ route('docentes.index') }}">
      <i class="ico fas fa-chalkboard-teacher"></i>CU-10 · Gestionar docentes</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-chalkboard-teacher"></i>CU-10 · Gestionar docentes<span class="nbg">Sin acceso</span></span>
    @endcan
    @can('ver grupos')
    <a class="ni {{ request()->routeIs('grupos.*') ? 'act':'' }}" href="{{ route('grupos.index') }}">
      <i class="ico fas fa-layer-group"></i>CU-11 · Gestionar grupos</a>
    <a class="ni {{ request()->routeIs('asignaciones.*') ? 'act':'' }}" href="{{ route('asignaciones.index') }}">
      <i class="ico fas fa-user-tie"></i>CU-12 · Asignar docente a grupos y materias</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-layer-group"></i>CU-11 · Gestionar grupos<span class="nbg">Sin acceso</span></span>
    <span class="ni pnd"><i class="ico fas fa-user-tie"></i>CU-12 · Asignar docente a grupos y materias<span class="nbg">Sin acceso</span></span>
    @endcan
  </div>
  <div class="sbdiv"></div>

  {{-- Módulo 4: Exámenes y Control Académico --}}
  <div class="sb-sec">
    <div class="sb-ttl">📝 Exámenes y Control Académico</div>
    @can('ver notas')
    <a class="ni {{ request()->routeIs('notas.index','notas.planilla','notas.create','notas.edit') ? 'act':'' }}" href="{{ route('notas.index') }}">
      <i class="ico fas fa-pencil-alt"></i>CU-13 · Registrar notas de exámenes</a>
    <a class="ni {{ request()->routeIs('notas.calcular') ? 'act':'' }}" href="{{ route('notas.calcular') }}">
      <i class="ico fas fa-calculator"></i>CU-14 · Calcular nota final, promedio y estado</a>
    <a class="ni {{ request()->routeIs('notas.consultar') ? 'act':'' }}" href="{{ route('notas.consultar') }}">
      <i class="ico fas fa-search"></i>CU-15 · Consultar notas del postulante</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-pencil-alt"></i>CU-13 · Registrar notas de exámenes<span class="nbg">Sin acceso</span></span>
    <span class="ni pnd"><i class="ico fas fa-calculator"></i>CU-14 · Calcular nota final, promedio y estado<span class="nbg">Sin acceso</span></span>
    <span class="ni pnd"><i class="ico fas fa-search"></i>CU-15 · Consultar notas del postulante<span class="nbg">Sin acceso</span></span>
    @endcan
  </div>
  <div class="sbdiv"></div>

  {{-- Módulo 5: Panel Administrativo y Reportes --}}
  <div class="sb-sec">
    <div class="sb-ttl">📊 Panel Administrativo y Reportes</div>
    @can('procesar admision')
    <a class="ni {{ request()->routeIs('admision.primera') ? 'act':'' }}" href="{{ route('admision.primera') }}">
      <i class="ico fas fa-cogs"></i>CU-16 · Procesar admisión por primera opción</a>
    <a class="ni {{ request()->routeIs('admision.segunda') ? 'act':'' }}" href="{{ route('admision.segunda') }}">
      <i class="ico fas fa-exchange-alt"></i>CU-17 · Reasignar postulantes a segunda opción</a>
      <i class="ico fas fa-hand-pointer"></i>CU-17 · Asig. manual 2ª op.</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-cogs"></i>CU-16 · Procesar admisión por primera opción<span class="nbg">Sin acceso</span></span>
    <span class="ni pnd"><i class="ico fas fa-exchange-alt"></i>CU-17 · Reasignar postulantes a segunda opción<span class="nbg">Sin acceso</span></span>
    @endcan
    @can('publicar admision')
    <a class="ni {{ request()->routeIs('admision.publicacion') ? 'act':'' }}" href="{{ route('admision.publicacion') }}">
      <i class="ico fas fa-bullhorn"></i>CU-18 · Publicar resultado final de admisión</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-bullhorn"></i>CU-18 · Publicar resultado final de admisión<span class="nbg">Sin acceso</span></span>
    @endcan
    @can('ver reportes')
    <a class="ni {{ request()->routeIs('reportes.*') ? 'act':'' }}" href="{{ route('reportes.index') }}">
      <i class="ico fas fa-chart-bar"></i>CU-19 · Gestionar reportes y estadísticas</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-chart-bar"></i>CU-19 · Gestionar reportes y estadísticas<span class="nbg">Sin acceso</span></span>
    @endcan
    @can('ver comunicados')
    <a class="ni {{ request()->routeIs('comunicados.*') ? 'act':'' }}" href="{{ route('comunicados.index') }}">
      <i class="ico fas fa-bullhorn"></i>CU-21 · Gestionar comunicados</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-bullhorn"></i>CU-21 · Gestionar comunicados<span class="nbg">Sin acceso</span></span>
    @endcan
    <a class="ni" href="{{ route('resultados.publico') }}" target="_blank">
      <i class="ico fas fa-search"></i>CU-22 · Consulta pública de resultados</a>
  </div>
  <div class="sbdiv"></div>

  <div class="sb-sec">
    <a class="ni lgt" href="{{ route('logout') }}"><i class="ico fas fa-sign-out-alt"></i>Cerrar sesión</a>
  </div>
</nav>

<div class="cup-mn" id="cupMn">
  <div class="cup-cnt">
    @include('layouts.partials.alert')
    @yield('content')
  </div>
  <footer class="cup-ft">
    <span>© {{ date('Y') }} Sistema de Admisión CUP — FICCT</span>
    <span>Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones</span>
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
const sb=document.getElementById('cupSb'),mn=document.getElementById('cupMn'),tg=document.getElementById('sbTgl');
let col=window.innerWidth<769;
function apl(){if(col){sb.classList.remove('open');if(window.innerWidth>=769){sb.classList.add('collapsed');mn.classList.add('exp')}else{sb.classList.remove('collapsed');mn.classList.remove('exp')}}else{sb.classList.add('open');sb.classList.remove('collapsed');mn.classList.remove('exp')}}
apl();tg.addEventListener('click',()=>{col=!col;apl()});
window.addEventListener('resize',()=>{col=window.innerWidth<769;apl()});
document.addEventListener('click',e=>{const d=document.getElementById('usrDd');if(d&&!d.contains(e.target))d.classList.remove('open')});
window.addEventListener('beforeunload',()=>navigator.sendBeacon('{{ route("bitacora.page-close") }}',new URLSearchParams({_token:'{{ csrf_token() }}'})));
</script>
@stack('js')
</body>
</html>
