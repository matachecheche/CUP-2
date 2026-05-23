#!/usr/bin/env bash
# =============================================================================
#  renovar_cup.sh  — v1.0
#  Sistema de Admisión CUP
#
#  Qué hace este script:
#    1. Reemplaza COMPLETAMENTE el sistema visual (CSS, layout, login, nav, panel)
#       → Estilo nuevo: "Institucional Andino" — verde oliva, oro, crema, tipografía
#         Crimson Pro + DM Sans. NADA parecido a SB Admin ni al proyecto anterior.
#    2. Renombra módulos según nuevo.odt (nombres exactos del documento)
#    3. Amplía la bitácora para registrar ABSOLUTAMENTE todas las acciones
#
#  USO:
#    chmod +x renovar_cup.sh
#    bash renovar_cup.sh
# =============================================================================

set -e
C='\033[0;36m'; G='\033[0;32m'; Y='\033[1;33m'; R='\033[0;31m'; N='\033[0m'
info()    { echo -e "${C}[INFO]${N}  $1"; }
ok()      { echo -e "${G}[OK]${N}    $1"; }
warn()    { echo -e "${Y}[WARN]${N}  $1"; }
error()   { echo -e "${R}[ERROR]${N} $1"; exit 1; }

[ -f "artisan" ] || error "Ejecuta desde la raíz del proyecto Laravel."

mkdir -p public/css public/js resources/views/{layouts,components,panel,bitacora,auth,users,roles,pages}
mkdir -p resources/views/layouts/partials

# =============================================================================
#  1. CSS PRINCIPAL — diseño "Institucional Andino"
#     Paleta: verde oliva #1a3a2a, oro #b8973e, crema #f5f0e8, blanco #ffffff
#     Tipografía: Crimson Pro (títulos) + DM Sans (cuerpo) — Google Fonts
# =============================================================================
info "Escribiendo nuevo CSS principal..."

cat > public/css/cup.css << 'ENDCSS'
/* ============================================================
   Sistema de Admisión CUP — Hoja de estilos principal
   Diseño: Institucional Andino
   Paleta: verde oliva · oro · crema · blanco
   Fuentes: Crimson Pro (display) · DM Sans (cuerpo)
   ============================================================ */

@import url('https://fonts.googleapis.com/css2?family=Crimson+Pro:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600;700&display=swap');

/* ── Variables ─────────────────────────────────────────────── */
:root {
  --verde:       #1a3a2a;
  --verde-2:     #254d38;
  --verde-3:     #2e6347;
  --verde-lite:  #d4e8dc;
  --oro:         #b8973e;
  --oro-lite:    #f0e2b6;
  --crema:       #f5f0e8;
  --crema-2:     #ede7d9;
  --txt:         #1c1c1c;
  --txt-2:       #4a4a4a;
  --txt-3:       #7a7a7a;
  --border:      #d6cfc2;
  --white:       #ffffff;
  --danger:      #a3290c;
  --danger-lite: #fde8e3;
  --warn:        #7a5c00;
  --warn-lite:   #fff8e1;
  --sidebar-w:   260px;
  --topbar-h:    60px;
  --radius:      8px;
  --shadow-sm:   0 1px 3px rgba(0,0,0,.08);
  --shadow:      0 4px 16px rgba(26,58,42,.12);
  --shadow-lg:   0 8px 32px rgba(26,58,42,.18);
  --font-display: 'Crimson Pro', Georgia, serif;
  --font-body:    'DM Sans', 'Helvetica Neue', sans-serif;
}

/* ── Reset & base ───────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html { font-size: 15px; -webkit-font-smoothing: antialiased; }

body {
  font-family: var(--font-body);
  background: var(--crema);
  color: var(--txt);
  line-height: 1.6;
  min-height: 100vh;
}

a { color: var(--verde-3); text-decoration: none; }
a:hover { color: var(--oro); }

/* ── Layout: topbar fija + sidebar fijo ─────────────────────── */
.cup-topbar {
  position: fixed;
  top: 0; left: 0; right: 0;
  height: var(--topbar-h);
  background: var(--verde);
  display: flex;
  align-items: center;
  padding: 0 1.25rem;
  gap: 1rem;
  z-index: 1000;
  border-bottom: 3px solid var(--oro);
}

.cup-topbar .brand {
  font-family: var(--font-display);
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--white);
  letter-spacing: .3px;
  display: flex;
  align-items: center;
  gap: .6rem;
}

.cup-topbar .brand-icon {
  width: 34px; height: 34px;
  border-radius: 6px;
  background: var(--oro);
  display: flex; align-items: center; justify-content: center;
  font-size: 1rem; color: var(--verde); font-weight: 700;
}

.cup-topbar .btn-toggle {
  background: none; border: none;
  color: rgba(255,255,255,.7);
  font-size: 1.1rem; cursor: pointer;
  padding: 6px 8px; border-radius: 6px;
  transition: .2s;
}
.cup-topbar .btn-toggle:hover { background: rgba(255,255,255,.1); color: #fff; }

.topbar-right {
  margin-left: auto;
  display: flex; align-items: center; gap: .75rem;
}

.topbar-user {
  display: flex; align-items: center; gap: .5rem;
  color: rgba(255,255,255,.85);
  font-size: .87rem; font-weight: 500;
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.14);
  border-radius: 30px;
  padding: .3rem .85rem;
  cursor: default;
}

.topbar-user .avatar {
  width: 28px; height: 28px;
  background: var(--oro);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: .78rem; font-weight: 700;
  color: var(--verde);
}

.topbar-dropdown { position: relative; }
.topbar-dropdown-menu {
  position: absolute; right: 0; top: calc(100% + 8px);
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-lg);
  min-width: 180px;
  padding: .4rem 0;
  display: none;
  z-index: 9999;
}
.topbar-dropdown:hover .topbar-dropdown-menu,
.topbar-dropdown.open .topbar-dropdown-menu { display: block; }

.topbar-dropdown-menu a {
  display: flex; align-items: center; gap: .6rem;
  padding: .5rem 1rem;
  font-size: .87rem;
  color: var(--txt-2);
  transition: .15s;
}
.topbar-dropdown-menu a:hover { background: var(--crema); color: var(--verde); }
.topbar-dropdown-menu .divider { border-top: 1px solid var(--border); margin: .3rem 0; }
.topbar-dropdown-menu a.danger { color: var(--danger); }
.topbar-dropdown-menu a.danger:hover { background: var(--danger-lite); }

/* ── Sidebar ────────────────────────────────────────────────── */
.cup-sidebar {
  position: fixed;
  top: var(--topbar-h); left: 0; bottom: 0;
  width: var(--sidebar-w);
  background: var(--white);
  border-right: 1px solid var(--border);
  overflow-y: auto;
  overflow-x: hidden;
  z-index: 900;
  transition: transform .28s ease;
}

.cup-sidebar.collapsed { transform: translateX(calc(-1 * var(--sidebar-w))); }

.cup-sidebar::-webkit-scrollbar { width: 4px; }
.cup-sidebar::-webkit-scrollbar-track { background: transparent; }
.cup-sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

/* Sección de usuario en sidebar */
.sidebar-user {
  padding: 1.1rem 1.2rem;
  border-bottom: 1px solid var(--crema-2);
  background: var(--crema);
  display: flex; align-items: center; gap: .75rem;
}
.sidebar-user .av {
  width: 38px; height: 38px; border-radius: 50%;
  background: var(--verde);
  display: flex; align-items: center; justify-content: center;
  font-size: .95rem; font-weight: 700; color: var(--oro);
  flex-shrink: 0;
}
.sidebar-user-info { min-width: 0; }
.sidebar-user-info .name {
  font-size: .9rem; font-weight: 600; color: var(--txt);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.sidebar-user-info .role {
  font-size: .73rem; color: var(--txt-3);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* Nav items */
.sidebar-section {
  padding: .6rem 1rem .2rem;
}
.sidebar-section-title {
  font-size: .65rem;
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: var(--txt-3);
  padding: .5rem .2rem .2rem;
}

.nav-item {
  display: flex; align-items: center; gap: .65rem;
  padding: .52rem .8rem;
  border-radius: 6px;
  margin-bottom: 2px;
  color: var(--txt-2);
  font-size: .88rem;
  transition: .15s;
  position: relative;
}
.nav-item:hover { background: var(--crema); color: var(--verde); }
.nav-item.active {
  background: var(--verde-lite);
  color: var(--verde);
  font-weight: 600;
}
.nav-item.active::before {
  content: '';
  position: absolute;
  left: -1px; top: 20%; bottom: 20%;
  width: 3px; border-radius: 0 3px 3px 0;
  background: var(--oro);
}
.nav-item .icon {
  width: 20px; text-align: center;
  font-size: .88rem; flex-shrink: 0;
  color: var(--txt-3);
}
.nav-item.active .icon, .nav-item:hover .icon { color: var(--verde-3); }
.nav-item.pending {
  color: var(--txt-3);
  cursor: default; pointer-events: none;
}
.nav-item.pending:hover { background: none; }
.nav-badge {
  margin-left: auto; flex-shrink: 0;
  font-size: .6rem; font-weight: 700;
  background: var(--crema-2);
  color: var(--txt-3);
  border-radius: 10px;
  padding: 1px 6px;
}
.nav-item.active .nav-badge {
  background: var(--verde-lite);
  color: var(--verde-3);
}

.sidebar-divider { border-top: 1px solid var(--crema-2); margin: .5rem 1rem; }

/* Logout en sidebar */
.nav-item.logout { color: var(--danger); }
.nav-item.logout:hover { background: var(--danger-lite); color: var(--danger); }
.nav-item.logout .icon { color: var(--danger); }

/* ── Contenido principal ────────────────────────────────────── */
.cup-main {
  margin-top: var(--topbar-h);
  margin-left: var(--sidebar-w);
  min-height: calc(100vh - var(--topbar-h));
  transition: margin-left .28s ease;
  display: flex; flex-direction: column;
}
.cup-main.expanded { margin-left: 0; }

.cup-content { flex: 1; padding: 2rem 2rem 1rem; }

/* ── Footer ─────────────────────────────────────────────────── */
.cup-footer {
  padding: .75rem 2rem;
  border-top: 1px solid var(--border);
  background: var(--white);
  display: flex; align-items: center; justify-content: space-between;
  font-size: .78rem; color: var(--txt-3);
}
.cup-footer a { color: var(--txt-3); }
.cup-footer a:hover { color: var(--verde); }

/* ── Page header ────────────────────────────────────────────── */
.page-header {
  margin-bottom: 1.75rem;
  border-bottom: 1px solid var(--border);
  padding-bottom: 1rem;
}
.page-header h1 {
  font-family: var(--font-display);
  font-size: 1.9rem;
  font-weight: 700;
  color: var(--verde);
  line-height: 1.2;
}
.page-header .subtitle {
  font-size: .88rem; color: var(--txt-3);
  margin-top: .2rem;
}
.breadcrumb {
  display: flex; align-items: center; gap: .4rem;
  list-style: none; font-size: .8rem; color: var(--txt-3);
  margin-top: .5rem;
}
.breadcrumb li + li::before { content: '/'; margin-right: .4rem; }
.breadcrumb a { color: var(--verde-3); }

/* ── Cards ──────────────────────────────────────────────────── */
.card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
}
.card-header {
  padding: .9rem 1.25rem;
  border-bottom: 1px solid var(--crema-2);
  font-size: .93rem;
  font-weight: 600;
  color: var(--verde);
  background: var(--crema);
  border-radius: var(--radius) var(--radius) 0 0;
  display: flex; align-items: center; gap: .5rem;
}
.card-header i { color: var(--oro); }
.card-body { padding: 1.25rem; }

/* ── Stats cards (panel) ────────────────────────────────────── */
.stat-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 1.75rem;
}
.stat-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.1rem 1.25rem;
  display: flex; align-items: center; gap: 1rem;
  box-shadow: var(--shadow-sm);
}
.stat-icon {
  width: 46px; height: 46px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.25rem; flex-shrink: 0;
}
.stat-icon.verde  { background: var(--verde-lite); color: var(--verde-3); }
.stat-icon.oro    { background: var(--oro-lite);   color: #7a5800; }
.stat-icon.rojo   { background: var(--danger-lite);color: var(--danger); }
.stat-icon.gris   { background: var(--crema-2);    color: var(--txt-3); }
.stat-value { font-size: 1.6rem; font-weight: 700; color: var(--txt); line-height: 1; }
.stat-label { font-size: .78rem; color: var(--txt-3); margin-top: .15rem; }

/* ── Módulo cards (panel) ───────────────────────────────────── */
.modulo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.25rem;
  margin-bottom: 2rem;
}
.modulo-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow-sm);
}
.modulo-card-header {
  display: flex; align-items: center; gap: .75rem;
  padding: .9rem 1.15rem;
  font-family: var(--font-display);
  font-size: 1.05rem;
  font-weight: 700;
  border-bottom: 1px solid var(--crema-2);
  cursor: pointer;
  user-select: none;
}
.modulo-card-header .num {
  width: 28px; height: 28px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: .82rem; font-weight: 800;
  flex-shrink: 0;
}
.modulo-card-header .chevron { margin-left: auto; font-size: .7rem; transition: .2s; }
.modulo-card-header.collapsed .chevron { transform: rotate(-90deg); }

.m1 .num { background: #d4e2f7; color: #1d4f8f; }
.m2 .num { background: #d8f0e6; color: #1a5c38; }
.m3 .num { background: #fde8cc; color: #8a4300; }
.m4 .num { background: #ede0f7; color: #5b2a8a; }
.m5 .num { background: #fce4e4; color: #8a1f1f; }

.m1 { border-top: 3px solid #3b82f6; }
.m2 { border-top: 3px solid #22c55e; }
.m3 { border-top: 3px solid #f97316; }
.m4 { border-top: 3px solid #a855f7; }
.m5 { border-top: 3px solid #ef4444; }

.modulo-card-body { padding: .6rem .8rem; }
.cu-row {
  display: flex; align-items: center; gap: .6rem;
  padding: .45rem .6rem;
  border-radius: 5px;
  margin-bottom: 1px;
  font-size: .86rem;
  color: var(--txt-2);
  transition: .15s;
}
.cu-row.link:hover { background: var(--crema); color: var(--verde); cursor: pointer; }
.cu-row.disabled { color: var(--txt-3); }
.cu-row a { color: inherit; display: flex; align-items: center; gap: .6rem; width: 100%; }
.cu-row a:hover { color: var(--verde); }
.cu-tag {
  font-size: .63rem; font-weight: 700;
  padding: 1px 5px; border-radius: 4px;
  flex-shrink: 0; min-width: 40px; text-align: center;
}
.cu-tag.done    { background: #d4edda; color: #1a5c38; border: 1px solid #a3d9b5; }
.cu-tag.pending { background: var(--crema-2); color: var(--txt-3); border: 1px solid var(--border); }
.cu-row .cu-icon { width: 16px; text-align: center; font-size: .82rem; color: var(--txt-3); flex-shrink: 0; }
.cu-row.link:hover .cu-icon { color: var(--verde-3); }
.cu-pending-label { margin-left: auto; font-size: .65rem; color: var(--txt-3); flex-shrink: 0; }

/* ── Tablas ──────────────────────────────────────────────────── */
.table-wrapper { overflow-x: auto; }
table.cup-table {
  width: 100%;
  border-collapse: collapse;
  font-size: .88rem;
}
.cup-table th {
  background: var(--crema);
  color: var(--verde);
  font-weight: 700;
  font-size: .78rem;
  text-transform: uppercase;
  letter-spacing: .05em;
  padding: .65rem 1rem;
  border-bottom: 2px solid var(--border);
  white-space: nowrap;
}
.cup-table td {
  padding: .7rem 1rem;
  border-bottom: 1px solid var(--crema-2);
  color: var(--txt-2);
  vertical-align: middle;
}
.cup-table tbody tr:hover { background: #fafaf7; }
.cup-table tbody tr:last-child td { border-bottom: none; }

/* ── Badges ──────────────────────────────────────────────────── */
.badge {
  display: inline-flex; align-items: center;
  padding: 2px 9px; border-radius: 20px;
  font-size: .72rem; font-weight: 700;
}
.badge-verde   { background: var(--verde-lite);  color: var(--verde); }
.badge-oro     { background: var(--oro-lite);    color: #5c4200; }
.badge-rojo    { background: var(--danger-lite); color: var(--danger); }
.badge-gris    { background: var(--crema-2);     color: var(--txt-3); }
.badge-azul    { background: #dbeafe;            color: #1d4f8f; }
.badge-violeta { background: #ede0f7;            color: #5b2a8a; }

/* ── Botones ─────────────────────────────────────────────────── */
.btn {
  display: inline-flex; align-items: center; gap: .4rem;
  padding: .5rem 1.1rem; border-radius: 6px;
  font-size: .87rem; font-weight: 600;
  cursor: pointer; border: 1px solid transparent;
  transition: .18s; white-space: nowrap;
}
.btn-primary {
  background: var(--verde); color: var(--white);
  border-color: var(--verde);
}
.btn-primary:hover { background: var(--verde-2); color: var(--white); box-shadow: var(--shadow-sm); }
.btn-oro {
  background: var(--oro); color: var(--white);
  border-color: var(--oro);
}
.btn-oro:hover { background: #9c7e34; color: var(--white); }
.btn-outline {
  background: transparent; color: var(--verde);
  border-color: var(--verde);
}
.btn-outline:hover { background: var(--verde-lite); }
.btn-danger {
  background: var(--danger); color: var(--white);
  border-color: var(--danger);
}
.btn-danger:hover { background: #7c1e09; color: var(--white); }
.btn-sm { padding: .32rem .75rem; font-size: .8rem; }
.btn-xs { padding: .2rem .55rem; font-size: .74rem; }

/* ── Formularios ─────────────────────────────────────────────── */
.form-label {
  display: block;
  font-size: .83rem; font-weight: 600;
  color: var(--txt); margin-bottom: .35rem;
}
.form-control, .form-select {
  width: 100%;
  padding: .52rem .85rem;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 6px;
  font-family: var(--font-body);
  font-size: .88rem;
  color: var(--txt);
  transition: border-color .15s, box-shadow .15s;
  appearance: none; -webkit-appearance: none;
}
.form-control:focus, .form-select:focus {
  outline: none;
  border-color: var(--verde-3);
  box-shadow: 0 0 0 3px rgba(46,99,71,.15);
}
.form-control::placeholder { color: var(--txt-3); }
.form-select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z' fill='%234a4a4a'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right .65rem center;
  background-size: 12px;
  padding-right: 2.2rem;
}
.form-row { display: grid; gap: 1rem; }
.form-row.cols-2 { grid-template-columns: 1fr 1fr; }
.form-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
.form-hint { font-size: .76rem; color: var(--txt-3); margin-top: .25rem; }
.form-check { display: flex; align-items: center; gap: .5rem; }
.form-check input[type="checkbox"] {
  width: 16px; height: 16px;
  border: 1px solid var(--border);
  border-radius: 4px; cursor: pointer;
  accent-color: var(--verde);
}

/* Grupo de permisos en roles */
.perm-group { border: 1px solid var(--border); border-radius: 6px; overflow: hidden; margin-bottom: .75rem; }
.perm-group-header {
  background: var(--crema);
  padding: .55rem .9rem;
  font-size: .78rem;
  font-weight: 700;
  color: var(--verde);
  display: flex; align-items: center; justify-content: space-between;
}
.perm-group-body {
  padding: .6rem .9rem;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: .3rem;
}

/* ── Alertas / mensajes ─────────────────────────────────────── */
.alert {
  display: flex; align-items: flex-start; gap: .6rem;
  padding: .75rem 1rem;
  border-radius: 6px;
  font-size: .87rem;
  margin-bottom: 1rem;
}
.alert-success { background: #e8f5ee; color: #1a5c38; border: 1px solid #a3d9b5; }
.alert-danger  { background: var(--danger-lite); color: var(--danger); border: 1px solid #f5b8a8; }
.alert-warn    { background: var(--warn-lite); color: var(--warn); border: 1px solid #ffe082; }
.alert ul { margin: .3rem 0 0 1rem; padding: 0; }

/* ── Bitácora ────────────────────────────────────────────────── */
.log-accion { font-weight: 500; color: var(--txt); }
.log-ip { font-family: 'Courier New', monospace; font-size: .8rem; color: var(--txt-3); }
.log-ts { font-size: .8rem; color: var(--txt-3); }
.log-modulo {
  font-size: .68rem; font-weight: 700;
  padding: 1px 6px; border-radius: 3px;
  text-transform: uppercase; letter-spacing: .04em;
}

/* ── Rol badge en tabla usuarios ────────────────────────────── */
.rol-admin      { background: #1a3a2a22; color: #1a3a2a; border: 1px solid #1a3a2a44; }
.rol-docente    { background: #1d4f8f22; color: #1d4f8f; border: 1px solid #1d4f8f44; }
.rol-postulante { background: #b8973e22; color: #7a5800; border: 1px solid #b8973e44; }

/* ── Paginación ──────────────────────────────────────────────── */
.pagination { display: flex; gap: .3rem; list-style: none; flex-wrap: wrap; }
.page-item .page-link {
  padding: .38rem .7rem; border-radius: 5px;
  border: 1px solid var(--border);
  color: var(--verde-3); font-size: .84rem;
  transition: .15s;
}
.page-item.active .page-link { background: var(--verde); color: #fff; border-color: var(--verde); }
.page-item .page-link:hover { background: var(--crema); }

/* ── Toast / Swal override ───────────────────────────────────── */
.swal2-popup { font-family: var(--font-body) !important; border-radius: 10px !important; }

/* ── Responsive ──────────────────────────────────────────────── */
@media (max-width: 768px) {
  .cup-sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
  .cup-sidebar.open { transform: translateX(0); }
  .cup-main { margin-left: 0; }
  .form-row.cols-2, .form-row.cols-3 { grid-template-columns: 1fr; }
  .cup-content { padding: 1.25rem 1rem; }
  .modulo-grid { grid-template-columns: 1fr; }
}
ENDCSS
ok "public/css/cup.css"

# =============================================================================
#  2. LAYOUT PRINCIPAL — layouts/ap.blade.php
#     Reemplaza SB Admin por layout propio "Institucional Andino"
# =============================================================================
info "Reescribiendo layout principal..."

cat > resources/views/layouts/ap.blade.php << 'BLADE'
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
            <i class="icon fas fa-journal-whills"></i> Registro de Auditoría
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
BLADE
ok "resources/views/layouts/ap.blade.php"

# =============================================================================
#  3. PARTIALS: alert
# =============================================================================
cat > resources/views/layouts/partials/alert.blade.php << 'BLADE'
@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Swal !== 'undefined') {
        Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false,
            timer:2800, timerProgressBar:true })
            .fire({ icon:'success', title:@json(session('success')) });
    }
});
</script>
@endif

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-times-circle"></i>
    <div><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
</div>
@endif
BLADE

# =============================================================================
#  4. LOGIN
# =============================================================================
info "Reescribiendo login..."

cat > resources/views/auth/login.blade.php << 'BLADE'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar — Sistema de Admisión CUP</title>
    <link href="{{ asset('css/cup.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: var(--crema);
            background-image:
                repeating-linear-gradient(
                    0deg, transparent, transparent 38px,
                    rgba(26,58,42,.04) 38px, rgba(26,58,42,.04) 39px
                ),
                repeating-linear-gradient(
                    90deg, transparent, transparent 38px,
                    rgba(26,58,42,.04) 38px, rgba(26,58,42,.04) 39px
                );
        }
        .login-wrap {
            width: 100%; max-width: 420px; padding: 1.5rem;
        }
        .login-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }
        .login-head {
            background: var(--verde);
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            border-bottom: 4px solid var(--oro);
        }
        .login-head .escudo {
            width: 64px; height: 64px; border-radius: 50%;
            background: var(--oro);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto .9rem;
            font-size: 1.6rem; color: var(--verde); font-weight: 900;
        }
        .login-head h1 {
            font-family: var(--font-display);
            font-size: 1.5rem; font-weight: 700;
            color: var(--white); margin: 0;
        }
        .login-head p {
            font-size: .8rem; color: rgba(255,255,255,.6);
            margin-top: .25rem;
        }
        .login-body { padding: 1.75rem 2rem 2rem; }
        .field { margin-bottom: 1rem; }
        .input-wrap {
            position: relative;
        }
        .input-wrap .inp-icon {
            position: absolute; left: .8rem; top: 50%;
            transform: translateY(-50%);
            color: var(--txt-3); font-size: .88rem;
        }
        .input-wrap .form-control {
            padding-left: 2.4rem;
        }
        .btn-login {
            width: 100%;
            background: var(--verde);
            color: var(--white);
            border: none; border-radius: 7px;
            padding: .75rem;
            font-family: var(--font-body);
            font-size: .95rem; font-weight: 700;
            cursor: pointer; transition: .2s;
            display: flex; align-items: center; justify-content: center; gap: .5rem;
        }
        .btn-login:hover { background: var(--verde-2); transform: translateY(-1px); box-shadow: var(--shadow); }
        .forgot { text-align: center; margin-top: 1rem; }
        .forgot a { font-size: .82rem; color: var(--txt-3); }
        .forgot a:hover { color: var(--verde); }
        .login-footer {
            background: var(--crema); border-top: 1px solid var(--border);
            text-align: center; padding: .6rem;
            font-size: .7rem; color: var(--txt-3);
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-head">
            <div class="escudo">C</div>
            <h1>Sistema de Admisión CUP</h1>
            <p>Curso Preuniversitario — Facultad de Ingeniería</p>
        </div>

        <div class="login-body">
            @if($errors->any())
            @foreach($errors->all() as $err)
            <div class="alert alert-danger" style="margin-bottom:.75rem;">
                <i class="fas fa-exclamation-circle"></i> {{ $err }}
            </div>
            @endforeach
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="field">
                    <label class="form-label" for="email">Correo institucional</label>
                    <div class="input-wrap">
                        <i class="inp-icon fas fa-envelope"></i>
                        <input id="email" type="email" name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            placeholder="usuario@cup.edu.bo"
                            autocomplete="email" autofocus>
                    </div>
                </div>

                <div class="field">
                    <label class="form-label" for="password">Contraseña</label>
                    <div class="input-wrap">
                        <i class="inp-icon fas fa-lock"></i>
                        <input id="password" type="password" name="password"
                            class="form-control"
                            placeholder="••••••••"
                            autocomplete="current-password">
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Ingresar al Sistema
                </button>

                <div class="forgot">
                    <a href="{{ route('password.request') }}">
                        <i class="fas fa-key" style="margin-right:.3rem;"></i>
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            </form>
        </div>

        <div class="login-footer">
            © {{ date('Y') }} CUP — Todos los derechos reservados
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
BLADE
ok "resources/views/auth/login.blade.php"

# =============================================================================
#  5. PANEL PRINCIPAL — panel/index.blade.php
# =============================================================================
info "Reescribiendo panel/index..."

cat > resources/views/panel/index.blade.php << 'BLADE'
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
            <div class="stat-value" style="font-size:1rem; font-weight:700; color:var(--verde)">Auditoría</div>
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
                    <i class="cu-icon fas fa-journal-whills"></i> Registro de auditoría
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
BLADE
ok "resources/views/panel/index.blade.php"

# =============================================================================
#  6. VISTAS DE USUARIOS
# =============================================================================
info "Reescribiendo vistas de usuarios..."

cat > resources/views/users/index.blade.php << 'BLADE'
@extends('layouts.ap')
@section('title', 'Usuarios del Sistema')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="page-header">
    <h1>Gestión de Usuarios</h1>
    <p class="subtitle">Administración de cuentas de acceso al sistema</p>
    <ol class="breadcrumb">
        <li><a href="{{ route('panel') }}">Inicio</a></li>
        <li>Usuarios</li>
    </ol>
</div>

@can('crear usuarios')
<div style="margin-bottom:1rem;">
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Nuevo Usuario
    </a>
</div>
@endcan

<div class="card">
    <div class="card-header">
        <i class="fas fa-users-cog"></i> Usuarios registrados
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table id="tablaUsuarios" class="cup-table" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td style="color:var(--txt-3); font-size:.8rem;">{{ $loop->iteration }}</td>
                        <td style="font-weight:500;">{{ $user->name }}</td>
                        <td style="color:var(--txt-3); font-size:.87rem;">{{ $user->email }}</td>
                        <td>
                            @foreach($user->getRoleNames() as $rol)
                                @php
                                    $cls = match($rol) {
                                        'Administrador del Sistema' => 'rol-admin',
                                        'Docente'                   => 'rol-docente',
                                        'Postulante'                => 'rol-postulante',
                                        default                     => '',
                                    };
                                @endphp
                                <span class="badge {{ $cls }}">{{ $rol }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge {{ $user->activo ? 'badge-verde' : 'badge-gris' }}">
                                {{ $user->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:.4rem; flex-wrap:wrap;">
                                @can('editar usuarios')
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('eliminar usuarios')
                                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm {{ $user->activo ? 'btn-danger' : 'btn-primary' }}"
                                        title="{{ $user->activo ? 'Desactivar' : 'Activar' }}"
                                        onclick="return confirm('¿Confirmar cambio de estado?')">
                                        <i class="fas fa-{{ $user->activo ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(document).ready(function(){
    $('#tablaUsuarios').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 15,
        order: [[0,'asc']]
    });
});
</script>
@endpush
@endsection
BLADE

cat > resources/views/users/create.blade.php << 'BLADE'
@extends('layouts.ap')
@section('title', 'Crear Usuario')

@section('content')
<div class="page-header">
    <h1>Crear Usuario</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('panel') }}">Inicio</a></li>
        <li><a href="{{ route('users.index') }}">Usuarios</a></li>
        <li>Crear</li>
    </ol>
</div>

<div class="card" style="max-width:720px;">
    <div class="card-header"><i class="fas fa-user-plus"></i> Nuevo usuario</div>
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-row cols-2">
                <div>
                    <label class="form-label">Nombre completo *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label class="form-label">Correo electrónico *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label class="form-label">Contraseña *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Confirmar contraseña *</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Rol *</label>
                    <select name="role" class="form-select" required>
                        <option value="">— Seleccionar —</option>
                        @foreach($roles as $rol)
                        <option value="{{ $rol->name }}" {{ old('role') == $rol->name ? 'selected' : '' }}>
                            {{ $rol->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:flex; gap:.75rem; margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE

cat > resources/views/users/edit.blade.php << 'BLADE'
@extends('layouts.ap')
@section('title', 'Editar Usuario')

@section('content')
<div class="page-header">
    <h1>Editar Usuario</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('panel') }}">Inicio</a></li>
        <li><a href="{{ route('users.index') }}">Usuarios</a></li>
        <li>Editar</li>
    </ol>
</div>

<div class="card" style="max-width:720px;">
    <div class="card-header"><i class="fas fa-user-edit"></i> Editando: {{ $user->name }}</div>
    <div class="card-body">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-row cols-2">
                <div>
                    <label class="form-label">Nombre completo *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div>
                    <label class="form-label">Correo electrónico *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div>
                    <label class="form-label">Nueva contraseña <span style="color:var(--txt-3); font-weight:400;">(dejar vacío = no cambiar)</span></label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div>
                    <label class="form-label">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                <div>
                    <label class="form-label">Rol *</label>
                    <select name="role" class="form-select" required>
                        <option value="">— Seleccionar —</option>
                        @foreach($roles as $rol)
                        <option value="{{ $rol->name }}" {{ $user->hasRole($rol->name) ? 'selected' : '' }}>
                            {{ $rol->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:flex; gap:.75rem; margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE
ok "Vistas de usuarios"

# =============================================================================
#  7. VISTAS DE ROLES
# =============================================================================
info "Reescribiendo vistas de roles..."

cat > resources/views/roles/index.blade.php << 'BLADE'
@extends('layouts.ap')
@section('title', 'Roles y Permisos')

@section('content')
<div class="page-header">
    <h1>Roles y Permisos</h1>
    <p class="subtitle">Configuración de accesos por tipo de usuario</p>
    <ol class="breadcrumb">
        <li><a href="{{ route('panel') }}">Inicio</a></li>
        <li>Roles</li>
    </ol>
</div>

@can('crear roles')
<div style="margin-bottom:1rem;">
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Rol
    </a>
</div>
@endcan

<div class="card">
    <div class="card-header"><i class="fas fa-user-shield"></i> Roles del sistema</div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="cup-table">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Permisos asignados</th>
                        <th>Vista previa</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php $protegidos = ['Administrador del Sistema', 'Docente', 'Postulante']; @endphp
                    @foreach($roles as $rol)
                    <tr>
                        <td>
                            <strong>{{ $rol->name }}</strong>
                            @if(in_array($rol->name, $protegidos))
                            <span class="badge badge-oro" style="margin-left:.4rem; font-size:.65rem;">base</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-verde">{{ $rol->permissions->count() }} permisos</span>
                        </td>
                        <td>
                            @foreach($rol->permissions->take(3) as $p)
                            <span class="badge badge-gris" style="margin:.1rem;">{{ $p->name }}</span>
                            @endforeach
                            @if($rol->permissions->count() > 3)
                            <span style="font-size:.75rem; color:var(--txt-3);">+{{ $rol->permissions->count()-3 }} más</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:.4rem;">
                                @can('editar roles')
                                <a href="{{ route('roles.edit', $rol) }}" class="btn btn-sm btn-outline" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('eliminar roles')
                                @if(!in_array($rol->name, $protegidos))
                                <form action="{{ route('roles.destroy', $rol) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar el rol {{ $rol->name }}?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
BLADE

cat > resources/views/roles/create.blade.php << 'BLADE'
@extends('layouts.ap')
@section('title', 'Crear Rol')

@section('content')
<div class="page-header">
    <h1>Crear Rol</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('panel') }}">Inicio</a></li>
        <li><a href="{{ route('roles.index') }}">Roles</a></li>
        <li>Crear</li>
    </ol>
</div>

<div class="card">
    <div class="card-header"><i class="fas fa-shield-alt"></i> Nuevo rol</div>
    <div class="card-body">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div style="max-width:400px; margin-bottom:1.5rem;">
                <label class="form-label">Nombre del rol *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <label class="form-label">Permisos * <span style="color:var(--txt-3); font-weight:400;">(selecciona al menos uno)</span></label>

            @foreach($permisos as $modulo => $lista)
            <div class="perm-group">
                <div class="perm-group-header">
                    <span>{{ $modulo }}</span>
                    <button type="button" class="btn btn-xs btn-outline"
                        onclick="toggleGrupo('g_{{ Str::slug($modulo) }}')">
                        Sel / Des
                    </button>
                </div>
                <div class="perm-group-body" id="g_{{ Str::slug($modulo) }}">
                    @foreach($lista as $permiso)
                    <label class="form-check">
                        <input type="checkbox" name="permission[]"
                            value="{{ $permiso->id }}"
                            {{ in_array($permiso->id, old('permission', [])) ? 'checked' : '' }}>
                        <span style="font-size:.83rem;">{{ $permiso->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div style="display:flex; gap:.75rem; margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                <a href="{{ route('roles.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
function toggleGrupo(id) {
    const checks = document.querySelectorAll('#' + id + ' input[type=checkbox]');
    const all = Array.from(checks).every(c => c.checked);
    checks.forEach(c => c.checked = !all);
}
</script>
@endpush
@endsection
BLADE

cat > resources/views/roles/edit.blade.php << 'BLADE'
@extends('layouts.ap')
@section('title', 'Editar Rol')

@section('content')
<div class="page-header">
    <h1>Editar Rol: {{ $role->name }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('panel') }}">Inicio</a></li>
        <li><a href="{{ route('roles.index') }}">Roles</a></li>
        <li>Editar</li>
    </ol>
</div>

<div class="card">
    <div class="card-header"><i class="fas fa-edit"></i> Editando: {{ $role->name }}</div>
    <div class="card-body">
        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf @method('PUT')
            <div style="max-width:400px; margin-bottom:1.5rem;">
                <label class="form-label">Nombre del rol *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
            </div>

            <label class="form-label">Permisos *</label>
            @foreach($permisos as $modulo => $lista)
            <div class="perm-group">
                <div class="perm-group-header">
                    <span>{{ $modulo }}</span>
                    <button type="button" class="btn btn-xs btn-outline"
                        onclick="toggleGrupo('g_{{ Str::slug($modulo) }}')">
                        Sel / Des
                    </button>
                </div>
                <div class="perm-group-body" id="g_{{ Str::slug($modulo) }}">
                    @foreach($lista as $permiso)
                    <label class="form-check">
                        <input type="checkbox" name="permission[]"
                            value="{{ $permiso->id }}"
                            {{ in_array($permiso->name, $permisosRol) ? 'checked' : '' }}>
                        <span style="font-size:.83rem;">{{ $permiso->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div style="display:flex; gap:.75rem; margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
                <a href="{{ route('roles.index') }}" class="btn btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@push('js')
<script>
function toggleGrupo(id) {
    const checks = document.querySelectorAll('#' + id + ' input[type=checkbox]');
    const all = Array.from(checks).every(c => c.checked);
    checks.forEach(c => c.checked = !all);
}
</script>
@endpush
@endsection
BLADE
ok "Vistas de roles"

# =============================================================================
#  8. BITÁCORA — middleware, trait, vista y migración actualizados
#     Registra ABSOLUTAMENTE todas las acciones del CUP
# =============================================================================
info "Actualizando sistema de bitácora..."

cat > app/Http/Middleware/BitacoraMiddleware.php << 'PHP'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

/**
 * Bitácora automática — registra TODAS las rutas nombradas del sistema CUP.
 * Para acciones con detalle adicional (crear, editar) se usa también BitacoraTrait.
 */
class BitacoraMiddleware
{
    // Rutas a ignorar (AJAX, métricas, assets, page-close)
    protected array $ignorar = [
        'bitacora.page-close',
    ];

    // Mapa completo de rutas → descripción legible (todos los módulos CUP)
    protected array $mapa = [

        // ── Módulo 1: Seguridad ──────────────────────────────────────
        'panel'                             => '[Seguridad] Accedió al panel de control',
        'login'                             => '[Seguridad] Visitó la página de inicio de sesión',
        'logout'                            => '[Seguridad] Cerró sesión',
        'password.request'                  => '[Seguridad] Visitó recuperación de contraseña',
        'password.email'                    => '[Seguridad] Solicitó enlace de recuperación',
        'password.reset'                    => '[Seguridad] Visitó formulario de nueva contraseña',
        'password.update'                   => '[Seguridad] Restableció su contraseña',

        'users.index'                       => '[Usuarios] Listó usuarios del sistema',
        'users.create'                      => '[Usuarios] Abrió formulario de creación de usuario',
        'users.store'                       => '[Usuarios] Creó un nuevo usuario',
        'users.show'                        => '[Usuarios] Vio detalle de usuario',
        'users.edit'                        => '[Usuarios] Abrió formulario de edición de usuario',
        'users.update'                      => '[Usuarios] Actualizó datos de usuario',
        'users.destroy'                     => '[Usuarios] Cambió estado de usuario (activar/desactivar)',
        'users.perfil'                      => '[Usuarios] Consultó su propio perfil',

        'roles.index'                       => '[Roles] Listó roles y permisos',
        'roles.create'                      => '[Roles] Abrió formulario de creación de rol',
        'roles.store'                       => '[Roles] Creó un nuevo rol',
        'roles.edit'                        => '[Roles] Abrió formulario de edición de rol',
        'roles.update'                      => '[Roles] Actualizó un rol',
        'roles.destroy'                     => '[Roles] Eliminó un rol',

        'bitacora.index'                    => '[Auditoría] Consultó el registro de auditoría',

        // ── Módulo 2: Gestión Académica ──────────────────────────────
        'gestiones.index'                   => '[Gestión Académica] Listó gestiones académicas',
        'gestiones.create'                  => '[Gestión Académica] Abrió formulario nueva gestión',
        'gestiones.store'                   => '[Gestión Académica] Creó una gestión académica',
        'gestiones.edit'                    => '[Gestión Académica] Editó una gestión académica',
        'gestiones.update'                  => '[Gestión Académica] Actualizó gestión académica',
        'gestiones.destroy'                 => '[Gestión Académica] Eliminó una gestión académica',

        'carreras.index'                    => '[Gestión Académica] Listó carreras',
        'carreras.create'                   => '[Gestión Académica] Abrió formulario nueva carrera',
        'carreras.store'                    => '[Gestión Académica] Creó una carrera',
        'carreras.edit'                     => '[Gestión Académica] Editó una carrera',
        'carreras.update'                   => '[Gestión Académica] Actualizó una carrera',
        'carreras.destroy'                  => '[Gestión Académica] Eliminó una carrera',
        'carreras.cupos'                    => '[Gestión Académica] Definió cupos para carrera (CU-11)',

        'materias.index'                    => '[Gestión Académica] Listó materias del CUP',
        'materias.create'                   => '[Gestión Académica] Abrió formulario nueva materia',
        'materias.store'                    => '[Gestión Académica] Creó una materia',
        'materias.edit'                     => '[Gestión Académica] Editó una materia',
        'materias.update'                   => '[Gestión Académica] Actualizó una materia',
        'materias.destroy'                  => '[Gestión Académica] Eliminó una materia',

        // ── Módulo 3: Postulantes y Docentes ────────────────────────
        'postulantes.index'                 => '[Postulantes] Listó postulantes (CU-09)',
        'postulantes.create'                => '[Postulantes] Abrió formulario registro postulante',
        'postulantes.store'                 => '[Postulantes] Registró un postulante (CU-05)',
        'postulantes.show'                  => '[Postulantes] Consultó detalle de postulante',
        'postulantes.edit'                  => '[Postulantes] Abrió edición de postulante',
        'postulantes.update'                => '[Postulantes] Actualizó datos de postulante',
        'postulantes.destroy'               => '[Postulantes] Eliminó un postulante',
        'postulantes.cargar-documentos'     => '[Postulantes] Cargó requisitos del postulante (CU-06)',
        'postulantes.validar'               => '[Postulantes] Validó requisitos del postulante (CU-07)',
        'postulantes.opciones-carrera'      => '[Postulantes] Registró opciones de carrera 1ª/2ª (CU-08)',
        'postulantes.estado'                => '[Postulantes] Consultó estado del postulante (CU-09)',

        'docentes.index'                    => '[Docentes] Listó docentes',
        'docentes.create'                   => '[Docentes] Abrió formulario registro docente',
        'docentes.store'                    => '[Docentes] Registró un docente (CU-14)',
        'docentes.show'                     => '[Docentes] Consultó perfil de docente',
        'docentes.edit'                     => '[Docentes] Editó datos de docente',
        'docentes.update'                   => '[Docentes] Actualizó datos de docente',
        'docentes.destroy'                  => '[Docentes] Eliminó un docente',
        'docentes.validar-perfil'           => '[Docentes] Validó perfil profesional (CU-15)',
        'docentes.carga-horaria'            => '[Docentes] Consultó carga horaria docente (CU-16)',

        // ── Módulo 4: Grupos, Horarios y Evaluación ──────────────────
        'grupos.index'                      => '[Grupos] Listó grupos del CUP',
        'grupos.create'                     => '[Grupos] Abrió formulario nuevo grupo',
        'grupos.store'                      => '[Grupos] Creó un grupo',
        'grupos.edit'                       => '[Grupos] Editó un grupo',
        'grupos.update'                     => '[Grupos] Actualizó un grupo',
        'grupos.destroy'                    => '[Grupos] Eliminó un grupo',
        'grupos.generar'                    => '[Grupos] Generó grupos automáticamente (CU-17)',
        'grupos.asignar-docente'            => '[Grupos] Asignó docente a grupo/materia (CU-18)',
        'grupos.validar-horario'            => '[Grupos] Validó cruce de horarios (CU-19)',
        'grupos.horario'                    => '[Grupos] Asignó horario y modalidad al grupo (CU-20)',
        'grupos.inscribir'                  => '[Grupos] Inscribió postulantes a grupo (CU-21)',

        'notas.index'                       => '[Evaluación] Listó notas del sistema',
        'notas.create'                      => '[Evaluación] Abrió formulario registro de notas',
        'notas.store'                       => '[Evaluación] Registró notas de examen (CU-22)',
        'notas.edit'                        => '[Evaluación] Editó notas de examen',
        'notas.update'                      => '[Evaluación] Actualizó notas de examen',
        'notas.calcular-final'              => '[Evaluación] Calculó nota final por materia (CU-23)',
        'notas.calcular-promedio'           => '[Evaluación] Calculó promedio general del postulante (CU-24)',
        'notas.determinar-condicion'        => '[Evaluación] Determinó condición aprobado/reprobado (CU-25)',
        'notas.propias'                     => '[Evaluación] Postulante consultó sus propias notas (CU-26)',

        // ── Módulo 5: Admisión y Reportes ────────────────────────────
        'admision.index'                    => '[Admisión] Accedió al módulo de admisión',
        'admision.procesar'                 => '[Admisión] Procesó admisión por primera opción (CU-27)',
        'admision.reasignar'                => '[Admisión] Reasignó postulantes a segunda opción (CU-28)',
        'admision.publicar'                 => '[Admisión] Publicó resultado final de admisión (CU-29)',
        'admision.resultado-propio'         => '[Admisión] Postulante consultó su resultado (CU-29)',

        'reportes.index'                    => '[Reportes] Accedió al módulo de reportes',
        'reportes.aprobados-reprobados'     => '[Reportes] Generó reporte aprobados/reprobados por grupo (CU-30)',
        'reportes.admitidos-carrera'        => '[Reportes] Generó reporte admitidos por carrera (CU-31)',
        'reportes.historico'                => '[Reportes] Consultó comparativo histórico entre gestiones (CU-32)',
        'reportes.estadisticas'             => '[Reportes] Consultó indicadores estadísticos (CU-33)',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!Auth::check()) {
            return $response;
        }

        $routeName = $request->route()?->getName() ?? '';

        if (in_array($routeName, $this->ignorar) || empty($routeName)) {
            return $response;
        }

        $accion = $this->mapa[$routeName]
            ?? '[Sistema] Visitó: ' . $request->method() . ' /' . $request->path();

        // Extraer módulo del tag entre corchetes
        $modulo = '';
        if (preg_match('/^\[([^\]]+)\]/', $accion, $m)) {
            $modulo = $m[1];
        }

        Bitacora::create([
            'user_id'      => Auth::id(),
            'usuario'      => Auth::user()->name,
            'accion'       => $accion,
            'modulo'       => $modulo,
            'metodo_http'  => $request->method(),
            'ruta'         => $request->path(),
            'fecha_hora'   => now(),
            'ip'           => $request->ip(),
            'user_agent'   => substr($request->userAgent() ?? '', 0, 255),
            'id_operacion' => null,
        ]);

        return $response;
    }
}
PHP
ok "app/Http/Middleware/BitacoraMiddleware.php"

cat > app/Traits/BitacoraTrait.php << 'PHP'
<?php

namespace App\Traits;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Registra acciones específicas de controladores en la bitácora.
 * Complementa al BitacoraMiddleware (que registra automáticamente por ruta).
 * Usar en controladores para agregar DETALLE extra (ej: nombre del registro creado).
 */
trait BitacoraTrait
{
    public function registrarEnBitacora(string $accion, $id_operacion = null, string $modulo = ''): void
    {
        try {
            $nombre = Auth::check() ? Auth::user()->name : 'Sistema';

            Bitacora::create([
                'user_id'      => Auth::id(),
                'usuario'      => $nombre,
                'accion'       => $accion,
                'modulo'       => $modulo,
                'metodo_http'  => request()->method(),
                'ruta'         => request()->path(),
                'fecha_hora'   => now(),
                'ip'           => request()->ip(),
                'user_agent'   => substr(request()->userAgent() ?? '', 0, 255),
                'id_operacion' => $id_operacion,
            ]);
        } catch (\Exception $e) {
            Log::error('BitacoraTrait: ' . $e->getMessage());
        }
    }
}
PHP
ok "app/Traits/BitacoraTrait.php"

# Migración: agregar columnas nuevas si no existen
cat > database/migrations/2026_05_23_000001_ampliar_bitacoras_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Amplía la tabla bitacoras con columnas necesarias para
 * el registro completo de acciones del sistema CUP.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bitacoras', function (Blueprint $table) {
            if (!Schema::hasColumn('bitacoras', 'modulo')) {
                $table->string('modulo', 60)->nullable()->after('accion');
            }
            if (!Schema::hasColumn('bitacoras', 'metodo_http')) {
                $table->string('metodo_http', 10)->nullable()->after('modulo');
            }
            if (!Schema::hasColumn('bitacoras', 'ruta')) {
                $table->string('ruta', 255)->nullable()->after('metodo_http');
            }
            if (!Schema::hasColumn('bitacoras', 'user_agent')) {
                $table->string('user_agent', 255)->nullable()->after('ip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bitacoras', function (Blueprint $table) {
            $table->dropColumn(['modulo', 'metodo_http', 'ruta', 'user_agent']);
        });
    }
};
PHP
ok "database/migrations/2026_05_23_000001_ampliar_bitacoras_table.php"

# Modelo Bitacora actualizado
cat > app/Models/Bitacora.php << 'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table    = 'bitacoras';
    protected $fillable = [
        'user_id',
        'usuario',
        'accion',
        'modulo',
        'metodo_http',
        'ruta',
        'fecha_hora',
        'ip',
        'user_agent',
        'id_operacion',
        'descripcion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
PHP

# Vista bitácora completamente nueva
cat > resources/views/bitacora/index.blade.php << 'BLADE'
@extends('layouts.ap')
@section('title', 'Registro de Auditoría')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
.log-modulo { font-size:.65rem; font-weight:700; padding:2px 6px; border-radius:3px;
    text-transform:uppercase; letter-spacing:.04em; white-space:nowrap; }
.mod-Seguridad      { background:#dbeafe; color:#1d4f8f; }
.mod-Usuarios       { background:#d4e8dc; color:#1a5c38; }
.mod-Roles          { background:#d8f0e6; color:#1a5c38; }
.mod-Auditoria      { background:#ede0f7; color:#5b2a8a; }
.mod-Postulantes    { background:#fde8cc; color:#8a4300; }
.mod-Docentes       { background:#fff8e1; color:#7a5c00; }
.mod-Grupos         { background:#fce4e4; color:#8a1f1f; }
.mod-Evaluacion     { background:#e0f7fa; color:#006064; }
.mod-Admision       { background:#f3e5f5; color:#6a1b9a; }
.mod-Reportes       { background:#e8f5e9; color:#2e7d32; }
.mod-default        { background:#f0f0f0; color:#555; }
.metodo { font-size:.68rem; font-weight:700; padding:1px 5px; border-radius:3px; font-family:monospace; }
.m-GET    { background:#e8f4fd; color:#0369a1; }
.m-POST   { background:#d4edda; color:#155724; }
.m-PUT    { background:#fff3cd; color:#856404; }
.m-PATCH  { background:#fff3cd; color:#856404; }
.m-DELETE { background:#fde8e3; color:#a3290c; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>Registro de Auditoría</h1>
    <p class="subtitle">Historial completo de acciones realizadas en el sistema</p>
    <ol class="breadcrumb">
        <li><a href="{{ route('panel') }}">Inicio</a></li>
        <li>Auditoría</li>
    </ol>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-journal-whills"></i> Bitácora del Sistema
        <span style="margin-left:auto; font-size:.8rem; color:var(--txt-3); font-weight:400;">
            {{ $bitacoras->count() }} registros
        </span>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table id="tablaBitacora" class="cup-table" style="width:100%; font-size:.84rem;">
                <thead>
                    <tr>
                        <th style="width:140px;">Fecha / Hora</th>
                        <th>Usuario</th>
                        <th>Módulo</th>
                        <th>Acción</th>
                        <th>Método</th>
                        <th>Ruta</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bitacoras as $log)
                    @php
                        $modKey = str_replace(' ', '', $log->modulo ?? '');
                        $modClass = 'mod-' . ($modKey ?: 'default');
                    @endphp
                    <tr>
                        <td class="log-ts" style="white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($log->fecha_hora)->format('d/m/Y H:i:s') }}
                        </td>
                        <td>
                            <strong style="font-size:.87rem;">{{ $log->usuario ?? '—' }}</strong>
                        </td>
                        <td>
                            @if($log->modulo)
                            <span class="log-modulo {{ $modClass }}">{{ $log->modulo }}</span>
                            @else
                            <span style="color:var(--txt-3);">—</span>
                            @endif
                        </td>
                        <td class="log-accion">
                            {{-- Eliminar el tag [Módulo] de la descripción para no duplicar --}}
                            {{ preg_replace('/^\[[^\]]+\]\s*/', '', $log->accion) }}
                        </td>
                        <td>
                            @if($log->metodo_http)
                            <span class="metodo m-{{ $log->metodo_http }}">{{ $log->metodo_http }}</span>
                            @endif
                        </td>
                        <td class="log-ip">{{ $log->ruta ?? '—' }}</td>
                        <td class="log-ip">{{ $log->ip ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
    $('#tablaBitacora').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        order: [[0, 'desc']],
        pageLength: 25,
        columnDefs: [{ targets: [5,6], orderable: false }]
    });
});
</script>
@endpush
@endsection
BLADE
ok "resources/views/bitacora/index.blade.php"

# =============================================================================
#  9. BitacoraController actualizado
# =============================================================================
cat > app/Http/Controllers/BitacoraController.php << 'PHP'
<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;

class BitacoraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver bitacora');
    }

    public function index()
    {
        $bitacoras = Bitacora::orderByDesc('fecha_hora')->limit(2000)->get();
        return view('bitacora.index', compact('bitacoras'));
    }
}
PHP
ok "app/Http/Controllers/BitacoraController.php"

# =============================================================================
#  10. QUITAR referencia a styles.css del layout y la plantilla heredada
# =============================================================================
info "Limpiando referencias CSS antiguas..."

# Si existe plantilla.blade.php (usada por panel antiguo), redirigir al nuevo layout
if [ -f "resources/views/plantilla.blade.php" ]; then
    cp resources/views/plantilla.blade.php resources/views/plantilla.blade.php.bak 2>/dev/null || true
    # panel/index ya usa @extends('layouts.ap'), no tocamos plantilla.blade.php
    warn "plantilla.blade.php respaldada como .bak (panel ya usa layouts.ap)"
fi

# adminlte.php: el proyecto ya no usa el paquete AdminLTE directamente
# (el layout es propio), pero si el archivo existe lo dejamos para no romper config
sed -i "s/'title' => '.*'/'title' => 'Admisión CUP'/" config/adminlte.php 2>/dev/null || true

# =============================================================================
#  11. Limpiar caché
# =============================================================================
info "Limpiando caché de Laravel..."
php artisan config:clear 2>/dev/null || true
php artisan route:clear  2>/dev/null || true
php artisan view:clear   2>/dev/null || true
php artisan cache:clear  2>/dev/null || true
ok "Caché limpiada"

# =============================================================================
#  RESUMEN
# =============================================================================
echo ""
echo -e "${G}══════════════════════════════════════════════════════════════${N}"
echo -e "${G}  RENOVACIÓN COMPLETADA — Sistema de Admisión CUP${N}"
echo -e "${G}══════════════════════════════════════════════════════════════${N}"
echo ""
echo -e "  ${C}DISEÑO NUEVO — Institucional Andino${N}"
echo "   • Layout 100% propio (sin SB Admin, sin dark mode azul)"
echo "   • Paleta: verde oliva #1a3a2a + oro #b8973e + crema #f5f0e8"
echo "   • Tipografía: Crimson Pro (títulos) + DM Sans (cuerpo)"
echo "   • Sidebar izquierdo limpio con secciones por módulo"
echo "   • Topbar con marca CUP y dropdown de usuario"
echo "   • Tablas, cards, forms, badges: diseño institucional propio"
echo ""
echo -e "  ${C}MÓDULOS RENOMBRADOS (nuevo.odt):${N}"
echo "   • Módulo 1 — Seguridad y Autenticación (CU-01 a CU-04)"
echo "   • Módulo 2 — Gestión Académica (CU-10 a CU-13)"
echo "   • Módulo 3 — Postulantes y Docentes (CU-05 a CU-09, CU-14 a CU-16)"
echo "   • Módulo 4 — Grupos, Horarios y Evaluación (CU-17 a CU-26)"
echo "   • Módulo 5 — Admisión y Reportes (CU-27 a CU-33)"
echo ""
echo -e "  ${C}BITÁCORA AMPLIADA:${N}"
echo "   • 60+ acciones mapeadas (todos los CU del documento)"
echo "   • Nuevas columnas: módulo, metodo_http, ruta, user_agent"
echo "   • Vista con DataTables, filtro por módulo, método HTTP coloreado"
echo "   • Registra AUTOMÁTICAMENTE toda navegación por middleware"
echo "   • BitacoraTrait para detalle extra en controladores"
echo ""
echo -e "  ${C}PRÓXIMOS PASOS:${N}"
echo "   1. php artisan migrate   (agrega columnas nuevas a bitacoras)"
echo "   2. Revisar que app/Http/Kernel.php incluya BitacoraMiddleware"
echo "      en el grupo 'web': \\App\\Http\\Middleware\\BitacoraMiddleware::class"
echo "   3. Subir cambios: git add -A && git commit -m 'feat: nuevo diseño institucional andino'"
echo ""
