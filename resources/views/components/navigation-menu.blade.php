<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark bg-black shadow" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <div class="sb-sidenav-menu-heading text-uppercase small text-secondary">Inicio</div>
                <a class="nav-link d-flex align-items-center gap-2" href="{{ route('panel') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    <span>Inicio</span>
                </a>

                {{-- PAQUETE 1 — CU1, CU2, CU3, CU4 --}}
                <div class="sb-sidenav-menu-heading text-uppercase small mt-3"
                     style="color:#60a5fa; font-size:0.67rem; letter-spacing:0.05em; padding:0.5rem 1rem 0.25rem;">
                    📦 Paquete 1 — Acceso y Seguridad
                </div>

                <a class="nav-link d-flex align-items-center gap-2" href="{{ route('login') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-sign-in-alt"></i></div>
                    <span>CU1 · Iniciar sesión</span>
                </a>

                <a class="nav-link d-flex align-items-center gap-2" href="{{ route('logout') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                    <span>CU2 · Cerrar sesión</span>
                </a>

                <a class="nav-link d-flex align-items-center gap-2" href="{{ route('users.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    <span>CU3 · Usuarios</span>
                </a>

                <a class="nav-link d-flex align-items-center gap-2" href="{{ route('roles.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-user-shield"></i></div>
                    <span>CU4 · Roles y Permisos</span>
                </a>

                {{-- PAQUETE 2 — CU5, CU6, CU13 --}}
                <div class="sb-sidenav-menu-heading text-uppercase small mt-3"
                     style="color:#34d399; font-size:0.67rem; letter-spacing:0.05em; padding:0.5rem 1rem 0.25rem;">
                    📦 Paquete 2 — Personas y Estructura
                </div>

                <a class="nav-link collapsed d-flex align-items-center gap-2" href="#"
                   data-bs-toggle="collapse" data-bs-target="#collapseEmpleados"
                   aria-expanded="false" aria-controls="collapseEmpleados">
                    <div class="sb-nav-link-icon"><i class="fas fa-id-card"></i></div>
                    <span>CU5 · Empleados</span>
                    <div class="sb-sidenav-collapse-arrow ms-auto"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseEmpleados" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link ps-4" href="{{ route('empleados.index') }}">
                            <i class="fas fa-list me-2"></i> Lista de Empleados
                        </a>
                        <a class="nav-link ps-4" href="{{ route('cargos.index') }}">
                            <i class="fas fa-briefcase me-2"></i> Cargos
                        </a>
                    </nav>
                </div>

                <a class="nav-link d-flex align-items-center gap-2" href="{{ route('residentes.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                    <span>CU6 · Residentes</span>
                </a>

                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-link"></i></div>
                    <span>CU13 · Vincular residente-unidad</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Ciclo 3</small>
                </span>

                {{-- PAQUETE 3 — CU7, CU8, CU9, CU10, CU15, CU16, CU17 --}}
                <div class="sb-sidenav-menu-heading text-uppercase small mt-3"
                     style="color:#fb923c; font-size:0.67rem; letter-spacing:0.05em; padding:0.5rem 1rem 0.25rem;">
                    📦 Paquete 3 — Gestión Operativa
                </div>

                <a class="nav-link collapsed d-flex align-items-center gap-2" href="#"
                   data-bs-toggle="collapse" data-bs-target="#collapseCuotas"
                   aria-expanded="false" aria-controls="collapseCuotas">
                    <div class="sb-nav-link-icon"><i class="fas fa-dollar-sign"></i></div>
                    <span>CU7 · Cuotas y Pagos</span>
                    <div class="sb-sidenav-collapse-arrow ms-auto"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseCuotas" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link ps-4" href="{{ route('cuotas.index') }}">
                            <i class="fas fa-list me-2"></i> Cuotas
                        </a>
                        <a class="nav-link ps-4" href="{{ route('tipos-cuotas.index') }}">
                            <i class="fas fa-tags me-2"></i> Tipos de Cuota
                        </a>
                        <a class="nav-link ps-4" href="{{ route('pagos.index') }}">
                            <i class="fas fa-money-bill me-2"></i> Pagos
                        </a>
                        <a class="nav-link ps-4" href="{{ route('multas.index') }}">
                            <i class="fas fa-exclamation-triangle me-2"></i> Multas
                        </a>
                    </nav>
                </div>

                <a class="nav-link collapsed d-flex align-items-center gap-2" href="#"
                   data-bs-toggle="collapse" data-bs-target="#collapseReservas"
                   aria-expanded="false" aria-controls="collapseReservas">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-check"></i></div>
                    <span>CU8 · Áreas y Reservas</span>
                    <div class="sb-sidenav-collapse-arrow ms-auto"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseReservas" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link ps-4" href="{{ route('areas-comunes.index') }}">
                            <i class="fas fa-map-marked me-2"></i> Áreas Comunes
                        </a>
                        <a class="nav-link ps-4" href="{{ route('reservas.index') }}">
                            <i class="fas fa-calendar me-2"></i> Reservas
                        </a>
                    </nav>
                </div>

                <a class="nav-link collapsed d-flex align-items-center gap-2" href="#"
                   data-bs-toggle="collapse" data-bs-target="#collapseMantenimiento"
                   aria-expanded="false" aria-controls="collapseMantenimiento">
                    <div class="sb-nav-link-icon"><i class="fas fa-tools"></i></div>
                    <span>CU9 · Mantenimientos</span>
                    <div class="sb-sidenav-collapse-arrow ms-auto"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseMantenimiento" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link ps-4" href="{{ route('mantenimientos.index') }}">
                            <i class="fas fa-list me-2"></i> Mantenimientos
                        </a>
                        <a class="nav-link ps-4" href="{{ route('empresas.index') }}">
                            <i class="fas fa-building me-2"></i> Empresas Externas
                        </a>
                    </nav>
                </div>

                <a class="nav-link collapsed d-flex align-items-center gap-2" href="#"
                   data-bs-toggle="collapse" data-bs-target="#collapseVisitas"
                   aria-expanded="false" aria-controls="collapseVisitas">
                    <div class="sb-nav-link-icon"><i class="fas fa-door-open"></i></div>
                    <span>CU10 · Visitas</span>
                    <div class="sb-sidenav-collapse-arrow ms-auto"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseVisitas" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link ps-4" href="{{ route('visitas.index') }}">
                            <i class="fas fa-list me-2"></i> Visitas
                        </a>
                        <a class="nav-link ps-4" href="{{ route('visitas.panel-guardia') }}">
                            <i class="fas fa-shield-alt me-2"></i> Panel Guardia
                        </a>
                        <a class="nav-link ps-4" href="{{ route('visitas.mostrar-validar-codigo') }}">
                            <i class="fas fa-key me-2"></i> Validar Código
                        </a>
                    </nav>
                </div>

                <a class="nav-link d-flex align-items-center gap-2" href="{{ route('empresas.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                    <span>CU15 · Empresas Externas</span>
                </a>

                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-flag"></i></div>
                    <span>CU16 · Denuncias e incidencias</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Ciclo 3</small>
                </span>

                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-bell"></i></div>
                    <span>CU17 · Notificaciones a residentes</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Ciclo 3</small>
                </span>

                {{-- PAQUETE 4 — CU11, CU12, CU14, CU18, CU19, CU20 --}}
                <div class="sb-sidenav-menu-heading text-uppercase small mt-3"
                     style="color:#a78bfa; font-size:0.67rem; letter-spacing:0.05em; padding:0.5rem 1rem 0.25rem;">
                    📦 Paquete 4 — Comunicación y Reportes
                </div>

                <a class="nav-link d-flex align-items-center gap-2" href="{{ route('comunicados.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                    <span>CU11 · Comunicados internos</span>
                </a>

                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                    <span>CU12 · Informes administrativos</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Ciclo 3</small>
                </span>

                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                    <span>CU14 · Reportes de pago</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Ciclo 3</small>
                </span>

                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <span>CU18 · Reclamos administrativos</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Ciclo 3</small>
                </span>

                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-star"></i></div>
                    <span>CU19 · Eventos comunitarios</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Ciclo 3</small>
                </span>

                <span class="nav-link d-flex align-items-center gap-2" style="color:#334155; cursor:default;">
                    <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                    <span>CU20 · Reclamos administrativos (II)</span>
                    <small class="ms-auto" style="font-size:0.62rem; color:#334155;">Ciclo 3</small>
                </span>

                <!-- OTROS -->
                <div class="sb-sidenav-menu-heading text-uppercase small text-secondary mt-3">Otros</div>
                <a class="nav-link d-flex align-items-center gap-2" href="{{ route('bitacora.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                    <span>Bitácora</span>
                </a>

                <a class="nav-link d-flex align-items-center gap-2 text-danger mt-2" href="{{ route('logout') }}">
                    <div class="sb-nav-link-icon"><i class="fa fa-sign-out"></i></div>
                    <span>Salir</span>
                </a>

            </div>
        </div>
    </nav>
</div>