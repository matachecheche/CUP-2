# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Laravel 11 (PHP 8.2) web application for the **CUP / FICCT admission system** ("Curso Prefacultativo"): it manages the full admission pipeline — applicant registration, academic configuration (gestiones, carreras, materias, cupos), group/teacher assignment, exam grading, and the final admission ranking. The UI is server-rendered Blade on AdminLTE 3 (Bootstrap 5), built with Vite. Database is **PostgreSQL** (`pgsql`), not the Laravel-default SQLite. App language and all domain vocabulary are Spanish.

## Commands

```bash
composer install                 # PHP deps
npm install                      # JS deps
php artisan key:generate         # after copying .env
php artisan migrate:fresh --seed # rebuild schema + seed roles/users/demo data
npm run dev                      # Vite dev server (HMR)
npm run build                    # production assets
php artisan serve                # serve app (APP_URL is :8002)
./vendor/bin/pint                # format PHP (Laravel Pint; StyleCI enforces it)
```

Tests (PHPUnit, two suites defined in `phpunit.xml`):

```bash
php artisan test                              # all tests
php artisan test --testsuite=Unit             # one suite (Unit | Feature)
php artisan test --filter=SomeTest            # single test class/method
```

Note: `phpunit.xml` does NOT override `DB_CONNECTION`, so tests run against the configured Postgres database, not in-memory SQLite (the sqlite lines are commented out). Be careful running tests against a live dev DB.

### Seeded credentials

`migrate:fresh --seed` creates one user per role, all with password `12345678`:
`admin@cup.edu.bo` (Administrador del Sistema), `docente@cup.edu.bo` (Docente), `postulante@cup.edu.bo` (Postulante).

## Architecture

**Routing → Controllers → Eloquent → Blade.** All routes are in `routes/web.php` (no API routes). Authenticated routes live under a single `auth` middleware group and are organized into 6 numbered modules matching use-case codes (CU-xx) referenced throughout the controllers and the bitácora map. There is no SPA/Flutter frontend despite the empty `flutter/` directory.

**Authorization — Spatie Permission.** Roles/permissions use `spatie/laravel-permission`. Middleware aliases `role`, `permission`, `role_or_permission` are registered in `bootstrap/app.php`. Controllers declare permissions in their constructor (e.g. `$this->middleware('permission:crear grupos')->only(...)`). Permissions are named in Spanish (`ver grupos`, `crear notas`, `procesar admision`). The `Administrador del Sistema` role gets all permissions; Docente and Postulante get curated subsets (see `RolesSeeder`).

**Audit log (bitácora) — two layers, both write to the `bitacoras` table:**
- `App\Http\Middleware\BitacoraMiddleware` (appended to the `web` group in `bootstrap/app.php`) auto-logs every authenticated request by mapping the **route name** to a human-readable Spanish action/module via its `$mapa` array. When adding a new named route, add a matching entry to `$mapa` so it is logged meaningfully (otherwise it falls back to a generic "Visitó …").
- `App\Traits\BitacoraTrait::registrarEnBitacora()` is used by controllers for explicit, semantic logging of business operations (e.g. "Procesó admisión …"). Both swallow exceptions and log to the Laravel log on failure.

**Domain model.** Core entities: `Gestion` (academic term — exactly one is `estado='en_curso'` and most workflows operate on that active gestion), `Carrera`, `Materia`, `CupoCarrera` (max seats per carrera×gestion), `Postulante`, `Docente`, `Grupo`, `Asignacion` (docente+materia+schedule on a grupo), `Nota`, `Admision`. Pivot `grupo_postulante` links applicants to groups.

**Key business rules (when touching these, preserve the invariants):**
- Group generation (`GrupoController::generar`, CU-11): number of groups = `ceil(inscritos / 70)`, capacity 70 each, turnos/modalidades cycle through fixed arrays.
- Teacher assignment (`GrupoController::asignarDocente`, CU-12): rejects schedule overlaps for the same docente/day, and caps a docente at 4 groups per gestion.
- Admission processing (`AdmisionController::procesar`, CU-16/17): wipes prior `Admision` rows for the active gestion, ranks approved applicants by `promedio_general` desc, fills `primera_opcion` seats first then `segunda_opcion`, marks the rest `no_admitido`; updates each `Postulante.estado`. `publicar` (CU-18) flips `publicado=true`.

**Postulante.estado lifecycle:** `inscrito → en_curso → aprobado → admitido | admitido_segunda_opcion | no_admitido` (also `reprobado`). State transitions are driven by the controllers above, not by the model.

**PDF reports** use `barryvdh/laravel-dompdf` with the `resources/views/plantillaPDF.blade.php` layout. The main app layout is `resources/views/plantilla.blade.php`.
