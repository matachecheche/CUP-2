@extends('plantilla')

@section('title', 'Inicio')

@section('content')

<style>
    body { background-color: #0f172a; }
    .dark-container { background-color: #0f172a; color: #e2e8f0; }
    .paquete-card {
        border-radius: 16px;
        border: 1px solid rgba(255,255,255,0.08);
        background-color: #1e293b;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .paquete-header {
        padding: 1rem 1.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 700;
        font-size: 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        user-select: none;
    }
    .paquete-body { padding: 1.1rem 1.5rem; }
    .cu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.65rem 0.9rem;
        border-radius: 10px;
        margin-bottom: 0.45rem;
        text-decoration: none;
        transition: background 0.2s;
        font-size: 0.93rem;
    }
    a.cu-item:hover { background-color: rgba(255,255,255,0.07); }
    .cu-item.disabled { color: #475569; pointer-events: none; cursor: default; }
    .cu-badge {
        font-size: 0.7rem; font-weight: 700;
        padding: 2px 7px; border-radius: 6px;
        min-width: 40px; text-align: center;
        flex-shrink: 0;
    }
    .badge-done    { background:#22c55e22; color:#4ade80; border:1px solid #4ade8055; }
    .badge-pending { background:#ffffff0a; color:#64748b; border:1px solid #33415533; }
    .pkg-1 { background: linear-gradient(135deg,#1e3a8a,#2563eb); }
    .pkg-2 { background: linear-gradient(135deg,#064e3b,#059669); }
    .pkg-3 { background: linear-gradient(135deg,#7c2d12,#ea580c); }
    .pkg-4 { background: linear-gradient(135deg,#4c1d95,#7c3aed); }
    .chevron { transition: transform 0.25s; }
    .paquete-header.collapsed .chevron { transform: rotate(-90deg); }
    .ciclo-tag {
        margin-left: auto;
        font-size: 0.65rem;
        color: #475569;
        flex-shrink: 0;
    }
</style>

<div class="container-fluid px-4 dark-container">

    <h1 class="mt-4 fw-bold text-light">Panel de Control</h1>
    <p class="text-secondary mb-4">Sistema de Gestión de Condominio — Módulos por paquete</p>

    {{-- PAQUETE 1 — Acceso y Seguridad: CU1, CU2, CU3, CU4 --}}
    <div class="paquete-card">
        <div class="paquete-header pkg-1 text-white"
             data-bs-toggle="collapse" data-bs-target="#paquete1" aria-expanded="true">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-shield-alt fa-lg"></i>
                <span>Paquete 1 — Gestión de Acceso y Seguridad</span>
            </div>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="paquete1" class="collapse show">
            <div class="paquete-body">

                <a href="{{ route('login') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU1</span>
                    <i class="fas fa-sign-in-alt" style="color:#60a5fa"></i>
                    Iniciar sesión
                </a>

                <a href="{{ route('logout') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU2</span>
                    <i class="fas fa-sign-out-alt" style="color:#60a5fa"></i>
                    Cerrar sesión
                </a>

                <a href="{{ route('users.index') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU3</span>
                    <i class="fas fa-users" style="color:#60a5fa"></i>
                    Gestionar usuarios
                </a>

                <a href="{{ route('roles.index') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU4</span>
                    <i class="fas fa-user-shield" style="color:#60a5fa"></i>
                    Gestionar roles y permisos
                </a>

            </div>
        </div>
    </div>

    {{-- PAQUETE 2 — Personas y Estructura: CU5, CU6, CU13 --}}
    <div class="paquete-card">
        <div class="paquete-header pkg-2 text-white"
             data-bs-toggle="collapse" data-bs-target="#paquete2" aria-expanded="true">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-users fa-lg"></i>
                <span>Paquete 2 — Gestión de Personas y Estructura</span>
            </div>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="paquete2" class="collapse show">
            <div class="paquete-body">

                <a href="{{ route('empleados.index') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU5</span>
                    <i class="fas fa-id-badge" style="color:#34d399"></i>
                    Gestionar empleados
                </a>

                <a href="{{ route('residentes.index') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU6</span>
                    <i class="fas fa-building" style="color:#34d399"></i>
                    Gestionar residentes
                </a>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU13</span>
                    <i class="fas fa-link"></i>
                    Vincular residente con unidad habitacional
                    <span class="ciclo-tag">Ciclo 3</span>
                </span>

            </div>
        </div>
    </div>

    {{-- PAQUETE 3 — Gestión Operativa: CU7, CU8, CU9, CU10, CU15, CU16, CU17 --}}
    <div class="paquete-card">
        <div class="paquete-header pkg-3 text-white"
             data-bs-toggle="collapse" data-bs-target="#paquete3" aria-expanded="true">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-cogs fa-lg"></i>
                <span>Paquete 3 — Gestión Operativa del Condominio</span>
            </div>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="paquete3" class="collapse show">
            <div class="paquete-body">

                <a href="{{ route('cuotas.index') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU7</span>
                    <i class="fas fa-dollar-sign" style="color:#fb923c"></i>
                    Gestionar pagos de cuotas
                </a>

                <a href="{{ route('reservas.index') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU8</span>
                    <i class="fas fa-calendar-check" style="color:#fb923c"></i>
                    Gestionar reservas de áreas comunes
                </a>

                <a href="{{ route('mantenimientos.index') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU9</span>
                    <i class="fas fa-tools" style="color:#fb923c"></i>
                    Gestionar mantenimientos
                </a>

                <a href="{{ route('visitas.index') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU10</span>
                    <i class="fas fa-door-open" style="color:#fb923c"></i>
                    Gestionar visitas al condominio
                </a>

                <a href="{{ route('empresas.index') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU15</span>
                    <i class="fas fa-handshake" style="color:#fb923c"></i>
                    Registrar contratación de empresa externa
                </a>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU16</span>
                    <i class="fas fa-flag"></i>
                    Gestionar denuncias o reportes de incidencias
                    <span class="ciclo-tag">Ciclo 3</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU17</span>
                    <i class="fas fa-bell"></i>
                    Enviar notificaciones a residentes
                    <span class="ciclo-tag">Ciclo 3</span>
                </span>

            </div>
        </div>
    </div>

    {{-- PAQUETE 4 — Comunicación y Reportes: CU11, CU12, CU14, CU18, CU19, CU20 --}}
    <div class="paquete-card">
        <div class="paquete-header pkg-4 text-white"
             data-bs-toggle="collapse" data-bs-target="#paquete4" aria-expanded="true">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-chart-bar fa-lg"></i>
                <span>Paquete 4 — Comunicación y Reportes</span>
            </div>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="paquete4" class="collapse show">
            <div class="paquete-body">

                <a href="{{ route('comunicados.index') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU11</span>
                    <i class="fas fa-envelope" style="color:#a78bfa"></i>
                    Gestionar comunicados internos
                </a>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU12</span>
                    <i class="fas fa-file-alt"></i>
                    Generar informes administrativos
                    <span class="ciclo-tag">Ciclo 3</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU14</span>
                    <i class="fas fa-receipt"></i>
                    Generar reportes de pagos
                    <span class="ciclo-tag">Ciclo 3</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU18</span>
                    <i class="fas fa-exclamation-circle"></i>
                    Gestionar reclamos administrativos
                    <span class="ciclo-tag">Ciclo 3</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU19</span>
                    <i class="fas fa-calendar-star"></i>
                    Gestionar eventos comunitarios
                    <span class="ciclo-tag">Ciclo 3</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU20</span>
                    <i class="fas fa-clipboard-list"></i>
                    Gestionar reclamos administrativos (II)
                    <span class="ciclo-tag">Ciclo 3</span>
                </span>

            </div>
        </div>
    </div>

</div>
@endsection