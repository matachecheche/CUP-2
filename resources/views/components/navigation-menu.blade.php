<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark bg-black shadow" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                {{-- ── INICIO ─────────────────────────────────────────────── --}}
                <div class="sb-sidenav-menu-heading text-uppercase small text-secondary">
                    Sistema de Admisión CUP
                </div>
                <a class="nav-link d-flex align-items-center gap-2" href="{{ route('panel') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    <span>Panel de Control</span>
                </a>

                {{-- ── MÓDULO 1 — Seguridad ────────────────────────────────── --}}
                <div class="sb-sidenav-menu-heading text-uppercase small mt-3"
                     style="color:#60a5fa; font-size:0.67rem; letter-spacing:0.05em; padding:0.5rem 1rem 0.25rem;">
                    🔐 Módulo 1 — Seguridad
                </div>

                @can('ver usuarios')
                <a class="nav-link d-flex align-items-center gap-2" href="{{ route('users.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                    <span>CU-04 · Usuarios</span>
                </a>
                @endcan

                @can('ver roles')
                <a class="nav-link d-flex align-items-center gap-2" href="{{ route('roles.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-user-shield"></i></div>
                    <span>CU-04 · Roles y Permisos</span>
                </a>
                @endcan

                @can('ver bitacora')
                <a class="nav-link d-flex align-items-center gap-2" href="{{ route('bitacora.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                    <span>Bitácora / Bitácora</span>
                </a>
                @endcan

                {{-- ── MÓDULO 2 — Gestión Académica ───────────────────────── --}}
                <div class="sb-sidenav-menu-heading text-uppercase small mt-3"
                     style="color:#34d399; font-size:0.67rem; letter-spacing:0.05em; padding:0.5rem 1rem 0.25rem;">
                    🎓 Módulo 2 — Gestión Académica
                </div>

                {{-- Gestiones (pendiente) --}}
                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    <span>CU-06 · Gestiones / Períodos</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Pendiente</small>
                </span>

                {{-- Carreras (pendiente) --}}
                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                    <span>CU-07 · Carreras</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Pendiente</small>
                </span>

                {{-- Cupos (pendiente) --}}
                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-sliders-h"></i></div>
                    <span>CU-08 · Cupos por carrera</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Pendiente</small>
                </span>

                {{-- Materias (pendiente) --}}
                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                    <span>CU-09 · Materias del CUP</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Pendiente</small>
                </span>

                {{-- ── MÓDULO 3 — Postulantes y Docentes ──────────────────── --}}
                <div class="sb-sidenav-menu-heading text-uppercase small mt-3"
                     style="color:#fb923c; font-size:0.67rem; letter-spacing:0.05em; padding:0.5rem 1rem 0.25rem;">
                    👥 Módulo 3 — Postulantes y Docentes
                </div>

                {{-- Postulantes (pendiente) --}}
                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                    <span>CU-05 · Postulantes</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Pendiente</small>
                </span>

                {{-- Docentes (pendiente) --}}
                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <span>CU-10 · Docentes</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Pendiente</small>
                </span>

                {{-- ── MÓDULO 4 — Grupos, Horarios y Evaluación ───────────── --}}
                <div class="sb-sidenav-menu-heading text-uppercase small mt-3"
                     style="color:#a78bfa; font-size:0.67rem; letter-spacing:0.05em; padding:0.5rem 1rem 0.25rem;">
                    📋 Módulo 4 — Grupos y Evaluación
                </div>

                {{-- Grupos (pendiente) --}}
                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-layer-group"></i></div>
                    <span>CU-11/12 · Grupos y Horarios</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Pendiente</small>
                </span>

                {{-- Notas (pendiente) --}}
                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-pen"></i></div>
                    <span>CU-13/15 · Notas y Evaluación</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Pendiente</small>
                </span>

                {{-- ── MÓDULO 5 — Admisión y Reportes ─────────────────────── --}}
                <div class="sb-sidenav-menu-heading text-uppercase small mt-3"
                     style="color:#f472b6; font-size:0.67rem; letter-spacing:0.05em; padding:0.5rem 1rem 0.25rem;">
                    🏆 Módulo 5 — Admisión y Reportes
                </div>

                {{-- Admisión (pendiente) --}}
                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-trophy"></i></div>
                    <span>CU-16/18 · Proceso de admisión</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Pendiente</small>
                </span>

                {{-- Reportes (pendiente) --}}
                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                    <span>CU-19 · Reportes y Estadísticas</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Pendiente</small>
                </span>

                {{-- ── SALIR ───────────────────────────────────────────────── --}}
                <div class="mt-3 mb-1 px-3">
                    <hr style="border-color: rgba(255,255,255,0.08); margin:0;">
                </div>
                <a class="nav-link d-flex align-items-center gap-2 text-danger" href="{{ route('logout') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                    <span>Cerrar sesión</span>
                </a>

            </div>
        </div>

        {{-- Footer del sidebar con nombre del sistema --}}
        <div class="sb-sidenav-footer" style="background:#0a0f1a; border-top:1px solid rgba(255,255,255,0.06);">
            <div class="small" style="color:#475569; font-size:0.7rem;">Sesión activa:</div>
            <span style="color:#94a3b8; font-size:0.82rem;">
                {{ auth()->user()?->name ?? 'Usuario' }}
            </span>
        </div>
    </nav>
</div>
