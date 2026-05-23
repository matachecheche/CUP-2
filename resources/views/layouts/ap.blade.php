<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CUP') — Sistema de Admisión</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ asset('css/cup.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    @stack('css')
</head>
<body>

{{-- ── TOPBAR ─────────────────────────────────────────────── --}}
<header class="cup-topbar">
    <button class="btn-toggle" id="sidebarToggle" title="Menú">
        <i class="fas fa-bars"></i>
    </button>
    <a href="{{ route('panel') }}" class="brand" style="text-decoration:none;">
        <div class="brand-icon">C</div>
        <span>Admisión <span style="color:var(--oro);">CUP</span></span>
    </a>

    <div class="topbar-right">
        <div class="topbar-dropdown" id="userDropdown">
            <div class="topbar-user" onclick="document.getElementById('userDropdown').classList.toggle('open')">
                <div class="avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                <span class="d-none d-sm-inline">{{ Auth::user()->name ?? 'Usuario' }}</span>
                <i class="fas fa-chevron-down" style="font-size:.65rem; margin-left:.25rem; color:rgba(255,255,255,.5);"></i>
            </div>
            <div class="topbar-dropdown-menu">
                <a href="{{ route('users.perfil') ?? '#' }}"><i class="fas fa-user-circle"></i> Mi perfil</a>
                <div class="divider"></div>
                <a href="{{ route('logout') }}" class="danger"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            </div>
        </div>
    </div>
</header>

{{-- ── SIDEBAR ─────────────────────────────────────────────── --}}
<nav class="cup-sidebar" id="cupSidebar">

    {{-- Usuario --}}
    <div class="sidebar-user">
        <div class="av">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
        <div class="sidebar-user-info">
            <div class="name">{{ Auth::user()->name ?? 'Usuario' }}</div>
            <div class="role">{{ Auth::user()->getRoleNames()->first() ?? 'Sin rol' }}</div>
        </div>
    </div>

    {{-- MÓDULO SEGURIDAD --}}
    <div class="sidebar-section">
        <div class="sidebar-section-title">🔐 Seguridad</div>

        <a class="nav-item {{ request()->routeIs('panel') ? 'active' : '' }}" href="{{ route('panel') }}">
            <i class="icon fas fa-th-large"></i> Panel de Control
        </a>

        @can('ver usuarios')
        <a class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
            <i class="icon fas fa-users-cog"></i> Gestión de Usuarios
        </a>
        @endcan

        @can('ver roles')
        <a class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
            <i class="icon fas fa-user-shield"></i> Roles y Permisos
        </a>
        @endcan

        @can('ver bitacora')
        <a class="nav-item {{ request()->routeIs('bitacora.*') ? 'active' : '' }}" href="{{ route('bitacora.index') }}">
            <i class="icon fas fa-journal-whills"></i> Registro de Bitácora
        </a>
        @endcan
    </div>

    <div class="sidebar-divider"></div>

    {{-- MÓDULO GESTIÓN ACADÉMICA --}}
    <div class="sidebar-section">
        <div class="sidebar-section-title">🎓 Gestión Académica</div>
        <span class="nav-item pending">
            <i class="icon fas fa-calendar-alt"></i> Gestiones Académicas
            <span class="nav-badge">Pronto</span>
        </span>
        <span class="nav-item pending">
            <i class="icon fas fa-graduation-cap"></i> Carreras
            <span class="nav-badge">Pronto</span>
        </span>
        <span class="nav-item pending">
            <i class="icon fas fa-sliders-h"></i> Cupos por Carrera
            <span class="nav-badge">Pronto</span>
        </span>
        <span class="nav-item pending">
            <i class="icon fas fa-book-open"></i> Materias del CUP
            <span class="nav-badge">Pronto</span>
        </span>
    </div>

    <div class="sidebar-divider"></div>

    {{-- MÓDULO POSTULANTES Y DOCENTES --}}
    <div class="sidebar-section">
        <div class="sidebar-section-title">👥 Personas</div>
        <span class="nav-item pending">
            <i class="icon fas fa-user-plus"></i> Postulantes
            <span class="nav-badge">Pronto</span>
        </span>
        <span class="nav-item pending">
            <i class="icon fas fa-chalkboard-teacher"></i> Docentes
            <span class="nav-badge">Pronto</span>
        </span>
    </div>

    <div class="sidebar-divider"></div>

    {{-- MÓDULO GRUPOS Y EVALUACIÓN --}}
    <div class="sidebar-section">
        <div class="sidebar-section-title">📋 Grupos y Notas</div>
        <span class="nav-item pending">
            <i class="icon fas fa-layer-group"></i> Grupos y Horarios
            <span class="nav-badge">Pronto</span>
        </span>
        <span class="nav-item pending">
            <i class="icon fas fa-pen-nib"></i> Registro de Notas
            <span class="nav-badge">Pronto</span>
        </span>
    </div>

    <div class="sidebar-divider"></div>

    {{-- MÓDULO ADMISIÓN --}}
    <div class="sidebar-section">
        <div class="sidebar-section-title">🏆 Admisión y Reportes</div>
        <span class="nav-item pending">
            <i class="icon fas fa-trophy"></i> Proceso de Admisión
            <span class="nav-badge">Pronto</span>
        </span>
        <span class="nav-item pending">
            <i class="icon fas fa-chart-bar"></i> Reportes y Estadísticas
            <span class="nav-badge">Pronto</span>
        </span>
    </div>

    <div class="sidebar-divider"></div>

    <div class="sidebar-section">
        <a class="nav-item logout" href="{{ route('logout') }}">
            <i class="icon fas fa-sign-out-alt"></i> Cerrar sesión
        </a>
    </div>

</nav>

{{-- ── CONTENIDO ────────────────────────────────────────────── --}}
<div class="cup-main" id="cupMain">
    <div class="cup-content">
        @include('layouts.partials.alert')
        @yield('content')
    </div>

    <footer class="cup-footer">
        <span>© {{ date('Y') }} Sistema de Admisión — Curso Preuniversitario (CUP)</span>
        <span>Facultad de Ingeniería</span>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
// Sidebar toggle
const sidebar = document.getElementById('cupSidebar');
const main    = document.getElementById('cupMain');
const toggle  = document.getElementById('sidebarToggle');
let collapsed = window.innerWidth < 769;

function applySidebar() {
    if (collapsed) {
        sidebar.classList.remove('open');
        if (window.innerWidth < 769) { sidebar.style.transform = ''; }
        else { sidebar.classList.add('collapsed'); main.classList.add('expanded'); }
    } else {
        sidebar.classList.add('open');
        sidebar.classList.remove('collapsed');
        main.classList.remove('expanded');
    }
}
applySidebar();
toggle.addEventListener('click', () => { collapsed = !collapsed; applySidebar(); });
window.addEventListener('resize', () => { collapsed = window.innerWidth < 769; applySidebar(); });

// Cerrar dropdown usuario al click fuera
document.addEventListener('click', e => {
    const dd = document.getElementById('userDropdown');
    if (dd && !dd.contains(e.target)) dd.classList.remove('open');
});

// Registrar cierre de pestaña
window.addEventListener('beforeunload', () => {
    navigator.sendBeacon('{{ route("bitacora.page-close") }}',
        new URLSearchParams({ _token: '{{ csrf_token() }}' }));
});
</script>
@stack('js')
</body>
</html>
