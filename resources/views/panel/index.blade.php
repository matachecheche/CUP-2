@extends('layouts.ap')
@section('title', 'Panel de Control')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')

<div class="page-header">
    <h1>Panel de Control</h1>
    <p class="subtitle">Sistema de Admisión — Curso Preuniversitario (CUP) &mdash; Facultad de Ingeniería</p>
    <ol class="breadcrumb">
        <li>Inicio</li>
    </ol>
</div>

{{-- Tarjetas de acceso rápido (solo módulos activos) --}}
<div class="stat-grid" style="margin-bottom:2rem;">
    @can('ver usuarios')
    <a class="stat-card" href="{{ route('users.index') }}" style="text-decoration:none;">
        <div class="stat-icon verde"><i class="fas fa-users-cog"></i></div>
        <div>
            <div class="stat-value" style="font-size:1rem; font-weight:700; color:var(--verde)">Usuarios</div>
            <div class="stat-label">Gestionar cuentas del sistema</div>
        </div>
    </a>
    @endcan

    @can('ver roles')
    <a class="stat-card" href="{{ route('roles.index') }}" style="text-decoration:none;">
        <div class="stat-icon oro"><i class="fas fa-user-shield"></i></div>
        <div>
            <div class="stat-value" style="font-size:1rem; font-weight:700; color:var(--verde)">Roles</div>
            <div class="stat-label">Gestionar roles y permisos</div>
        </div>
    </a>
    @endcan

    @can('ver bitacora')
    <a class="stat-card" href="{{ route('bitacora.index') }}" style="text-decoration:none;">
        <div class="stat-icon gris"><i class="fas fa-journal-whills"></i></div>
        <div>
            <div class="stat-value" style="font-size:1rem; font-weight:700; color:var(--verde)">Bitácora</div>
            <div class="stat-label">Registro de actividad del sistema</div>
        </div>
    </a>
    @endcan
</div>

{{-- Módulos del sistema --}}
<div class="modulo-grid">

    {{-- MÓDULO 1 — Seguridad --}}
    <div class="modulo-card m1">
        <div class="modulo-card-header" data-bs-toggle="collapse" data-bs-target="#m1body">
            <div class="num">1</div>
            <span>Seguridad y Autenticación</span>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="m1body" class="collapse show">
            <div class="modulo-card-body">
                <div class="cu-row link"><a href="{{ route('login') }}">
                    <span class="cu-tag done">CU-01</span>
                    <i class="cu-icon fas fa-sign-in-alt"></i> Iniciar sesión
                </a></div>
                <div class="cu-row link"><a href="{{ route('logout') }}">
                    <span class="cu-tag done">CU-02</span>
                    <i class="cu-icon fas fa-sign-out-alt"></i> Cerrar sesión
                </a></div>
                <div class="cu-row link"><a href="{{ route('password.request') }}">
                    <span class="cu-tag done">CU-03</span>
                    <i class="cu-icon fas fa-key"></i> Recuperar contraseña
                </a></div>
                @can('ver usuarios')
                <div class="cu-row link"><a href="{{ route('users.index') }}">
                    <span class="cu-tag done">CU-04</span>
                    <i class="cu-icon fas fa-users-cog"></i> Gestionar usuarios y roles
                </a></div>
                @endcan
                @can('ver bitacora')
                <div class="cu-row link"><a href="{{ route('bitacora.index') }}">
                    <span class="cu-tag done">AUD</span>
                    <i class="cu-icon fas fa-journal-whills"></i> Registro de bitácora
                </a></div>
                @endcan
            </div>
        </div>
    </div>

    {{-- MÓDULO 2 — Gestión Académica --}}
    <div class="modulo-card m2">
        <div class="modulo-card-header" data-bs-toggle="collapse" data-bs-target="#m2body">
            <div class="num">2</div>
            <span>Gestión Académica</span>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="m2body" class="collapse show">
            <div class="modulo-card-body">
                <div class="cu-row disabled">
                    <span class="cu-tag pending">CU-13</span>
                    <i class="cu-icon fas fa-calendar-alt"></i>
                    Gestionar gestiones académicas
                    <span class="cu-pending-label">Pendiente</span>
                </div>
                <div class="cu-row disabled">
                    <span class="cu-tag pending">CU-10</span>
                    <i class="cu-icon fas fa-graduation-cap"></i>
                    Gestionar carreras de la facultad
                    <span class="cu-pending-label">Pendiente</span>
                </div>
                <div class="cu-row disabled">
                    <span class="cu-tag pending">CU-11</span>
                    <i class="cu-icon fas fa-sliders-h"></i>
                    Definir cupos por carrera y gestión
                    <span class="cu-pending-label">Pendiente</span>
                </div>
                <div class="cu-row disabled">
                    <span class="cu-tag pending">CU-12</span>
                    <i class="cu-icon fas fa-book-open"></i>
                    Gestionar materias del CUP
                    <span class="cu-pending-label">Pendiente</span>
                </div>
            </div>
        </div>
    </div>

    {{-- MÓDULO 3 — Postulantes y Docentes --}}
    <div class="modulo-card m3">
        <div class="modulo-card-header" data-bs-toggle="collapse" data-bs-target="#m3body">
            <div class="num">3</div>
            <span>Postulantes y Docentes</span>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="m3body" class="collapse show">
            <div class="modulo-card-body">
                <div class="cu-row disabled"><span class="cu-tag pending">CU-05</span><i class="cu-icon fas fa-user-plus"></i> Registrar postulante <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-06</span><i class="cu-icon fas fa-file-upload"></i> Cargar requisitos del postulante <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-07</span><i class="cu-icon fas fa-check-circle"></i> Validar requisitos del postulante <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-08</span><i class="cu-icon fas fa-list-ol"></i> Seleccionar 1ª y 2ª opción de carrera <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-09</span><i class="cu-icon fas fa-search"></i> Consultar estado del postulante <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-14</span><i class="cu-icon fas fa-chalkboard-teacher"></i> Registrar docente con perfil profesional <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-15</span><i class="cu-icon fas fa-user-check"></i> Validar perfil profesional del docente <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-16</span><i class="cu-icon fas fa-clock"></i> Consultar carga horaria del docente <span class="cu-pending-label">Pendiente</span></div>
            </div>
        </div>
    </div>

    {{-- MÓDULO 4 — Grupos, Horarios y Evaluación --}}
    <div class="modulo-card m4">
        <div class="modulo-card-header" data-bs-toggle="collapse" data-bs-target="#m4body">
            <div class="num">4</div>
            <span>Grupos, Horarios y Evaluación</span>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="m4body" class="collapse show">
            <div class="modulo-card-body">
                <div class="cu-row disabled"><span class="cu-tag pending">CU-17</span><i class="cu-icon fas fa-magic"></i> Calcular y generar grupos automáticamente <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-18</span><i class="cu-icon fas fa-chalkboard"></i> Asignar docente a grupo y materia <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-19</span><i class="cu-icon fas fa-exclamation-triangle"></i> Validar cruces de horario <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-20</span><i class="cu-icon fas fa-calendar-week"></i> Asignar horarios y modalidad al grupo <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-21</span><i class="cu-icon fas fa-user-friends"></i> Inscribir postulantes a grupos <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-22</span><i class="cu-icon fas fa-pen-nib"></i> Registrar notas de exámenes (3 por materia) <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-23</span><i class="cu-icon fas fa-calculator"></i> Calcular nota final (30%+30%+40%) <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-24</span><i class="cu-icon fas fa-percent"></i> Calcular promedio general del postulante <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-25</span><i class="cu-icon fas fa-check-double"></i> Determinar condición aprobado/reprobado ≥60 <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-26</span><i class="cu-icon fas fa-eye"></i> Consultar notas del postulante <span class="cu-pending-label">Pendiente</span></div>
            </div>
        </div>
    </div>

    {{-- MÓDULO 5 — Admisión y Reportes --}}
    <div class="modulo-card m5">
        <div class="modulo-card-header" data-bs-toggle="collapse" data-bs-target="#m5body">
            <div class="num">5</div>
            <span>Admisión y Reportes</span>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div id="m5body" class="collapse show">
            <div class="modulo-card-body">
                <div class="cu-row disabled"><span class="cu-tag pending">CU-27</span><i class="cu-icon fas fa-trophy"></i> Procesar admisión por primera opción <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-28</span><i class="cu-icon fas fa-random"></i> Reasignar postulantes a segunda opción <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-29</span><i class="cu-icon fas fa-bullhorn"></i> Publicar resultado final de admisión <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-30</span><i class="cu-icon fas fa-file-alt"></i> Reporte aprobados/reprobados por grupo <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-31</span><i class="cu-icon fas fa-file-chart-line"></i> Reporte admitidos por carrera y gestión <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-32</span><i class="cu-icon fas fa-history"></i> Comparativo histórico entre gestiones <span class="cu-pending-label">Pendiente</span></div>
                <div class="cu-row disabled"><span class="cu-tag pending">CU-33</span><i class="cu-icon fas fa-chart-pie"></i> Indicadores estadísticos del proceso <span class="cu-pending-label">Pendiente</span></div>
            </div>
        </div>
    </div>

</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Collapse chevron animation
document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
    const target = document.querySelector(btn.dataset.bsTarget);
    if (!target) return;
    target.addEventListener('show.bs.collapse', () => btn.classList.remove('collapsed'));
    target.addEventListener('hide.bs.collapse', () => btn.classList.add('collapsed'));
});
</script>
@endpush
@endsection
