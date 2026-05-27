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
    @can('ver usuarios')
    <a class="ni {{ request()->routeIs('users.*') ? 'act':'' }}" href="{{ route('users.index') }}">
      <i class="ico fas fa-users-cog"></i>Gestión de Usuarios</a>
    @endcan
    @can('ver roles')
    <a class="ni {{ request()->routeIs('roles.*') ? 'act':'' }}" href="{{ route('roles.index') }}">
      <i class="ico fas fa-user-shield"></i>Roles y Permisos</a>
    @endcan
    @can('ver bitacora')
    <a class="ni {{ request()->routeIs('bitacora.*') ? 'act':'' }}" href="{{ route('bitacora.index') }}">
      <i class="ico fas fa-journal-whills"></i>Bitácora</a>
    @endcan
  </div>
  <div class="sbdiv"></div>

  {{-- Módulo 2: Registro de Postulantes --}}
  <div class="sb-sec">
    <div class="sb-ttl">👤 Registro de Postulantes</div>
    @can('ver postulantes')
    <a class="ni {{ request()->routeIs('postulantes.*') ? 'act':'' }}" href="{{ route('postulantes.index') }}">
      <i class="ico fas fa-user-plus"></i>Postulantes</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-user-plus"></i>Postulantes<span class="nbg">Sin acceso</span></span>
    @endcan
  </div>
  <div class="sbdiv"></div>

  {{-- Módulo 3: Gestión Académica --}}
  <div class="sb-sec">
    <div class="sb-ttl">🎓 Gestión Académica</div>
    @can('ver gestiones')
    <a class="ni {{ request()->routeIs('gestiones.*') ? 'act':'' }}" href="{{ route('gestiones.index') }}">
      <i class="ico fas fa-calendar-alt"></i>Gestiones Académicas</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-calendar-alt"></i>Gestiones<span class="nbg">Sin acceso</span></span>
    @endcan
    @can('ver carreras')
    <a class="ni {{ request()->routeIs('carreras.*') ? 'act':'' }}" href="{{ route('carreras.index') }}">
      <i class="ico fas fa-graduation-cap"></i>Carreras y Cupos</a>
    <a class="ni {{ request()->routeIs('cupos.*') ? 'act':'' }}" href="{{ route('cupos.index') }}">
      <i class="ico fas fa-sliders-h"></i>Cupos por Gestión</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-graduation-cap"></i>Carreras y Cupos<span class="nbg">Sin acceso</span></span>
    @endcan
    @can('ver materias')
    <a class="ni {{ request()->routeIs('materias.*') ? 'act':'' }}" href="{{ route('materias.index') }}">
      <i class="ico fas fa-book-open"></i>Materias del CUP</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-book-open"></i>Materias del CUP<span class="nbg">Sin acceso</span></span>
    @endcan
  </div>
  <div class="sbdiv"></div>

  {{-- Módulo 4: Asignación de Grupos y Docentes --}}
  <div class="sb-sec">
    <div class="sb-ttl">🏫 Grupos y Docentes</div>
    @can('ver docentes')
    <a class="ni {{ request()->routeIs('docentes.*') ? 'act':'' }}" href="{{ route('docentes.index') }}">
      <i class="ico fas fa-chalkboard-teacher"></i>Docentes</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-chalkboard-teacher"></i>Docentes<span class="nbg">Sin acceso</span></span>
    @endcan
    @can('ver grupos')
    <a class="ni {{ request()->routeIs('grupos.*') ? 'act':'' }}" href="{{ route('grupos.index') }}">
      <i class="ico fas fa-layer-group"></i>Grupos y Horarios</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-layer-group"></i>Grupos y Horarios<span class="nbg">Sin acceso</span></span>
    @endcan
  </div>
  <div class="sbdiv"></div>

  {{-- Módulo 5: Exámenes --}}
  <div class="sb-sec">
    <div class="sb-ttl">📝 Exámenes y Control Académico</div>
    @can('ver notas')
    <a class="ni {{ request()->routeIs('notas.*') ? 'act':'' }}" href="{{ route('notas.index') }}">
      <i class="ico fas fa-pen-nib"></i>Registro de Notas</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-pen-nib"></i>Registro de Notas<span class="nbg">Sin acceso</span></span>
    @endcan
  </div>
  <div class="sbdiv"></div>

  {{-- Módulo 6: Panel Administrativo --}}
  <div class="sb-sec">
    <div class="sb-ttl">📊 Panel Administrativo</div>
    @can('ver admision')
    <a class="ni {{ request()->routeIs('admision.*') ? 'act':'' }}" href="{{ route('admision.index') }}">
      <i class="ico fas fa-trophy"></i>Proceso de Admisión</a>
    @else
    <span class="ni pnd"><i class="ico fas fa-trophy"></i>Proceso de Admisión<span class="nbg">Sin acceso</span></span>
    @endcan
    <span class="ni pnd"><i class="ico fas fa-chart-bar"></i>Reportes y Estadísticas<span class="nbg">Ciclo 2</span></span>
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
