#!/usr/bin/env bash
# =============================================================================
#  fix_vistas_cup.sh
#  Corrige el panel de control y la vista de login para el Sistema CUP
#  Ejecutar desde la raíz del proyecto Laravel
# =============================================================================

set -e
GREEN='\033[0;32m'; CYAN='\033[0;36m'; NC='\033[0m'
info()    { echo -e "${CYAN}[INFO]${NC}  $1"; }
success() { echo -e "${GREEN}[OK]${NC}    $1"; }

# ── 1. PANEL DE CONTROL ───────────────────────────────────────────────────────
info "Reescribiendo panel/index.blade.php para el Sistema CUP..."

mkdir -p resources/views/panel

cat > resources/views/panel/index.blade.php << 'BLADE'
@extends('plantilla')

@section('title', 'Panel de Control — Admisión CUP')

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
        min-width: 42px; text-align: center;
        flex-shrink: 0;
    }
    .badge-done    { background:#22c55e22; color:#4ade80; border:1px solid #4ade8055; }
    .badge-pending { background:#ffffff0a; color:#64748b; border:1px solid #33415533; }
    .pkg-1 { background: linear-gradient(135deg,#1e3a8a,#2563eb); }
    .pkg-2 { background: linear-gradient(135deg,#064e3b,#059669); }
    .pkg-3 { background: linear-gradient(135deg,#7c2d12,#ea580c); }
    .pkg-4 { background: linear-gradient(135deg,#4c1d95,#7c3aed); }
    .pkg-5 { background: linear-gradient(135deg,#831843,#db2777); }
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
    <p class="text-secondary mb-4">
        Sistema de Admisión de Postulantes — Curso Preuniversitario (CUP)
    </p>

    {{-- PAQUETE 1 — Seguridad: CU-01 a CU-04 --}}
    <div class="paquete-card">
        <div class="paquete-header pkg-1 text-white"
             data-bs-toggle="collapse" data-bs-target="#paquete1" aria-expanded="true">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-shield-alt fa-lg"></i>
                <span>Módulo 1 — Seguridad y Autenticación</span>
            </div>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="paquete1" class="collapse show">
            <div class="paquete-body">

                <a href="{{ route('login') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU-01</span>
                    <i class="fas fa-sign-in-alt" style="color:#60a5fa"></i>
                    Iniciar sesión
                </a>

                <a href="{{ route('logout') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU-02</span>
                    <i class="fas fa-sign-out-alt" style="color:#60a5fa"></i>
                    Cerrar sesión
                </a>

                <a href="{{ route('password.request') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU-03</span>
                    <i class="fas fa-key" style="color:#60a5fa"></i>
                    Recuperar contraseña
                </a>

                @can('ver usuarios')
                <a href="{{ route('users.index') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU-04</span>
                    <i class="fas fa-users-cog" style="color:#60a5fa"></i>
                    Gestionar usuarios y roles
                </a>
                @endcan

                @can('ver roles')
                <a href="{{ route('roles.index') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">CU-04b</span>
                    <i class="fas fa-user-shield" style="color:#60a5fa"></i>
                    Gestionar roles y permisos
                </a>
                @endcan

                @can('ver bitacora')
                <a href="{{ route('bitacora.index') }}" class="cu-item text-slate-200">
                    <span class="cu-badge badge-done">AUD</span>
                    <i class="fas fa-clipboard-list" style="color:#60a5fa"></i>
                    Auditoría / Bitácora de accesos
                </a>
                @endcan

            </div>
        </div>
    </div>

    {{-- PAQUETE 2 — Gestión Académica: CU-10, CU-11, CU-12, CU-13 --}}
    <div class="paquete-card">
        <div class="paquete-header pkg-2 text-white"
             data-bs-toggle="collapse" data-bs-target="#paquete2" aria-expanded="true">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-university fa-lg"></i>
                <span>Módulo 2 — Gestión Académica</span>
            </div>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="paquete2" class="collapse show">
            <div class="paquete-body">

                {{-- CU-13: Gestiones --}}
                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-13</span>
                    <i class="fas fa-calendar-alt"></i>
                    Gestionar gestiones / períodos académicos
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                {{-- CU-10: Carreras --}}
                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-10</span>
                    <i class="fas fa-graduation-cap"></i>
                    Gestionar carreras de la facultad
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                {{-- CU-11: Cupos --}}
                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-11</span>
                    <i class="fas fa-sliders-h"></i>
                    Definir cupos por carrera y gestión
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                {{-- CU-12: Materias --}}
                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-12</span>
                    <i class="fas fa-book"></i>
                    Gestionar materias del CUP
                    <span class="ciclo-tag">Pendiente</span>
                </span>

            </div>
        </div>
    </div>

    {{-- PAQUETE 3 — Postulantes y Docentes: CU-05 a CU-09, CU-14 a CU-16 --}}
    <div class="paquete-card">
        <div class="paquete-header pkg-3 text-white"
             data-bs-toggle="collapse" data-bs-target="#paquete3" aria-expanded="true">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-users fa-lg"></i>
                <span>Módulo 3 — Postulantes y Docentes</span>
            </div>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="paquete3" class="collapse show">
            <div class="paquete-body">

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-05</span>
                    <i class="fas fa-user-plus"></i>
                    Registrar postulante
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-06</span>
                    <i class="fas fa-file-upload"></i>
                    Cargar requisitos del postulante (CI, libreta, título)
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-07</span>
                    <i class="fas fa-check-circle"></i>
                    Validar requisitos del postulante
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-08</span>
                    <i class="fas fa-list-ol"></i>
                    Seleccionar 1ª y 2ª opción de carrera
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-09</span>
                    <i class="fas fa-search"></i>
                    Consultar estado del postulante
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-14</span>
                    <i class="fas fa-chalkboard-teacher"></i>
                    Registrar docente con perfil profesional
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-15</span>
                    <i class="fas fa-user-check"></i>
                    Validar perfil profesional del docente
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-16</span>
                    <i class="fas fa-clock"></i>
                    Consultar carga horaria del docente
                    <span class="ciclo-tag">Pendiente</span>
                </span>

            </div>
        </div>
    </div>

    {{-- PAQUETE 4 — Grupos, Horarios y Evaluación: CU-17 a CU-26 --}}
    <div class="paquete-card">
        <div class="paquete-header pkg-4 text-white"
             data-bs-toggle="collapse" data-bs-target="#paquete4" aria-expanded="true">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-layer-group fa-lg"></i>
                <span>Módulo 4 — Grupos, Horarios y Evaluación</span>
            </div>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="paquete4" class="collapse show">
            <div class="paquete-body">

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-17</span>
                    <i class="fas fa-magic"></i>
                    Calcular y generar grupos automáticamente (máx. 60)
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-18</span>
                    <i class="fas fa-chalkboard"></i>
                    Asignar docente a grupo y materia
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-19</span>
                    <i class="fas fa-exclamation-triangle"></i>
                    Validar cruces de horario
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-20</span>
                    <i class="fas fa-calendar-week"></i>
                    Asignar horarios y modalidad al grupo
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-21</span>
                    <i class="fas fa-user-friends"></i>
                    Inscribir postulantes a grupos
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-22</span>
                    <i class="fas fa-pen"></i>
                    Registrar notas de exámenes (3 por materia)
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-23</span>
                    <i class="fas fa-calculator"></i>
                    Calcular nota final por materia (30%+30%+40%)
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-24</span>
                    <i class="fas fa-percent"></i>
                    Calcular promedio general del postulante
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-25</span>
                    <i class="fas fa-check-double"></i>
                    Determinar condición aprobado / reprobado (≥60 en 4 materias)
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-26</span>
                    <i class="fas fa-eye"></i>
                    Consultar notas del postulante
                    <span class="ciclo-tag">Pendiente</span>
                </span>

            </div>
        </div>
    </div>

    {{-- PAQUETE 5 — Admisión y Reportes: CU-27 a CU-33 --}}
    <div class="paquete-card">
        <div class="paquete-header pkg-5 text-white"
             data-bs-toggle="collapse" data-bs-target="#paquete5" aria-expanded="true">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-chart-bar fa-lg"></i>
                <span>Módulo 5 — Admisión y Reportes</span>
            </div>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="paquete5" class="collapse show">
            <div class="paquete-body">

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-27</span>
                    <i class="fas fa-trophy"></i>
                    Procesar admisión por primera opción de carrera
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-28</span>
                    <i class="fas fa-random"></i>
                    Reasignar postulantes a segunda opción
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-29</span>
                    <i class="fas fa-bullhorn"></i>
                    Publicar resultado final de admisión
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-30</span>
                    <i class="fas fa-file-alt"></i>
                    Reporte aprobados / reprobados por grupo
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-31</span>
                    <i class="fas fa-file-chart-line"></i>
                    Reporte admitidos por carrera y gestión
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-32</span>
                    <i class="fas fa-history"></i>
                    Comparativo histórico entre gestiones
                    <span class="ciclo-tag">Pendiente</span>
                </span>

                <span class="cu-item disabled">
                    <span class="cu-badge badge-pending">CU-33</span>
                    <i class="fas fa-chart-pie"></i>
                    Indicadores estadísticos del proceso
                    <span class="ciclo-tag">Pendiente</span>
                </span>

            </div>
        </div>
    </div>

</div>
@endsection
BLADE
success "resources/views/panel/index.blade.php reemplazado"

# ── 2. LOGIN ──────────────────────────────────────────────────────────────────
info "Reescribiendo resources/views/auth/login.blade.php para CUP..."

mkdir -p resources/views/auth

cat > resources/views/auth/login.blade.php << 'BLADE'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iniciar sesión — Sistema de Admisión CUP</title>

    <link href="{{ asset('css/plantilla.css') }}" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e3a5f);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }

        .card-login {
            border-radius: 20px;
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            color: #f1f5f9;
        }

        .login-logo {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.6rem;
            box-shadow: 0 4px 20px rgba(37,99,235,0.5);
        }

        .card-login h4 {
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .card-login .subtitle {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.55);
            margin-top: -4px;
        }

        .input-group-text {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-right: none;
            color: #94a3b8;
            border-radius: 10px 0 0 10px;
        }

        .form-control {
            background: rgba(255,255,255,0.08) !important;
            border: 1px solid rgba(255,255,255,0.15);
            border-left: none;
            color: #f1f5f9 !important;
            border-radius: 0 10px 10px 0 !important;
            padding: 11px 14px;
        }

        .form-control::placeholder { color: rgba(255,255,255,0.35); }
        .form-control:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(14,165,233,0.45);
            border-color: rgba(14,165,233,0.5);
        }

        .btn-login {
            border-radius: 10px;
            padding: 11px;
            font-weight: 700;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            border: none;
            color: #fff;
            transition: 0.25s;
            letter-spacing: 0.3px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14,165,233,0.45);
        }

        .forgot-link {
            color: rgba(255,255,255,0.55);
            font-size: 0.83rem;
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: #38bdf8; text-decoration: underline; }

        .faculty-badge {
            display: inline-block;
            font-size: 0.68rem;
            color: rgba(255,255,255,0.35);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 2px 10px;
            margin-top: 6px;
        }
    </style>
</head>

<body>
<div class="login-wrapper">
    <div class="card-login p-4">

        <div class="text-center mb-4">
            <div class="login-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h4>Sistema de Admisión CUP</h4>
            <p class="subtitle">Curso Preuniversitario — Acceso al sistema</p>
            <span class="faculty-badge">Facultad de Ingeniería</span>
        </div>

        @if ($errors->any())
            @foreach ($errors->all() as $item)
                <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ $item }}
                    <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="alert"></button>
                </div>
            @endforeach
        @endif

        <form action="/login" method="POST">
            @csrf

            <div class="input-group mb-3">
                <span class="input-group-text">
                    <i class="fas fa-envelope fa-sm"></i>
                </span>
                <input type="email" name="email"
                    class="form-control"
                    placeholder="Correo institucional"
                    value="{{ old('email') }}"
                    autocomplete="email" autofocus>
            </div>

            <div class="input-group mb-4">
                <span class="input-group-text">
                    <i class="fas fa-lock fa-sm"></i>
                </span>
                <input type="password" name="password"
                    class="form-control"
                    placeholder="Contraseña"
                    autocomplete="current-password">
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('password.request') }}" class="forgot-link">
                    <i class="fas fa-key me-1"></i> ¿Olvidaste tu contraseña?
                </a>
            </div>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
BLADE
success "resources/views/auth/login.blade.php actualizado"

# ── 3. Limpiar vistas cacheadas ───────────────────────────────────────────────
info "Limpiando caché de vistas..."
php artisan view:clear 2>/dev/null || true

echo ""
echo -e "${GREEN}══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}  Listo. Recarga el navegador — ya no hay referencias${NC}"
echo -e "${GREEN}  al condominio en el panel ni en el login.${NC}"
echo -e "${GREEN}══════════════════════════════════════════════════════════${NC}"
