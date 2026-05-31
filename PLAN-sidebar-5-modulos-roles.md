# Plan de ejecución — Sidebar de 5 módulos con control de acceso por rol

> **Para Claude Code Desktop.** Repo: `CUP-si1`, rama `feature/mejiav2`.
> Stack confirmado: **Laravel 11 (PHP 8.2) + AdminLTE 3 + Blade + Spatie Permission + PostgreSQL**.
> Ejecuta este plan tal cual. Primero verifica los archivos contra el repo real; los nombres de clases CSS y permisos ya fueron auditados y existen.

---

## Objetivo

1. Reestructurar el **sidebar** (`resources/views/layouts/ap.blade.php`) para que tenga **exactamente los mismos 5 módulos** que el dashboard, en el mismo orden y con los mismos títulos.
2. En **cada módulo del sidebar**, mostrar **todos sus casos de uso (CU)** siempre visibles. El que el rol puede usar se renderiza como enlace funcional; el que no, se renderiza **deshabilitado con la etiqueta "Sin acceso"** (nunca desaparece).
3. Aplicar el mismo criterio en el **dashboard** (`resources/views/panel/index.blade.php`): hoy varios CU **desaparecen** cuando el rol no tiene permiso. Deben quedar siempre visibles con "Sin acceso".
4. Garantizar coherencia para los **3 roles**: `Administrador del Sistema`, `Docente`, `Postulante`.

**Regla de oro:** el paquete (módulo) y el caso de uso SIEMPRE se muestran. Solo cambia si es enlace activo o estado "Sin acceso".

---

## Estado actual detectado (no asumir, ya auditado)

- El dashboard ya tiene **5 módulos** (`panel/index.blade.php`), pero el **sidebar tiene 6 secciones** desalineadas (`layouts/ap.blade.php`).
- En el dashboard, **solo CU-05 y los "Próximamente" tienen el estado "Sin acceso"**. CU-06…CU-18 usan `@can(...) ... @endcan` **sin `@else`**, por lo que **desaparecen** para Docente/Postulante. Esto viola la regla de oro y hay que corregirlo.
- **Inconsistencia de permisos:** en el sidebar el módulo de admisión se cierra con `@can('ver admision')`, pero en el dashboard CU-16/17/18 usan `@can('procesar admision')`. Hay que unificar (ver matriz).
- Existe el permiso `ver cupos` pero CU-08 se controla con `ver carreras`. Decidir y unificar (ver matriz).
- Clases CSS ya existentes para reutilizar (NO crear CSS nuevo): `public/css/cup.css`
  - Sidebar deshabilitado: `<span class="ni pnd"><i class="ico ..."></i>Texto<span class="nbg">Sin acceso</span></span>`
  - Dashboard deshabilitado: `<div class="cr2x dis"><span class="ctg pn">CU-XX</span><i class="ci2 ..."></i>Texto<span class="cpl">Sin acceso</span></div>`
  - Activo dashboard: `<div class="cr2x lnk"><a href="{{ route('...') }}"><span class="ctg dn">CU-XX</span>...<i class="ci2 ..."></i>Texto</a></div>`

---

## Los 5 módulos y sus casos de uso (fuente: dashboard actual)

| Módulo | CU | Acción | Ruta | Permiso (`@can`) |
|---|---|---|---|---|
| **1. Autenticación y Seguridad** | CU-01 | Iniciar sesión | `login` | público (todos) |
| | CU-02 | Cerrar sesión | `logout` | público (todos) |
| | CU-03 | Recuperar contraseña | `password.request` | público (todos) |
| | CU-04 | Gestionar usuarios y roles | `users.index` / `roles.index` | `ver usuarios` / `ver roles` |
| | — | Bitácora | `bitacora.index` | `ver bitacora` |
| **2. Registro de Postulantes y Gestión Académica** | CU-05 | Gestionar postulantes | `postulantes.index` | `ver postulantes` |
| | CU-06 | Gestionar gestiones académicas | `gestiones.index` | `ver gestiones` |
| | CU-07 | Gestionar carreras | `carreras.index` | `ver carreras` |
| | CU-08 | Definir cupos por carrera y gestión | `cupos.index` | `ver cupos` |
| | CU-09 | Gestionar materias del CUP | `materias.index` | `ver materias` |
| | CU-20 | Pasarela de pago | — | **Próximamente** (todos) |
| **3. Asignación de Grupos y Docentes** | CU-10 | Gestionar docentes | `docentes.index` | `ver docentes` |
| | CU-11 | Gestionar grupos | `grupos.index` | `ver grupos` |
| | CU-12 | Asignar docente a grupos y materias | `grupos.index` | `ver grupos` |
| **4. Exámenes y Control Académico** | CU-13 | Registrar notas de exámenes | `notas.index` | `ver notas` |
| | CU-14 | Calcular nota final, promedio y estado | `notas.index` | `ver notas` |
| | CU-15 | Consultar notas del postulante | `notas.index` | `ver notas` |
| **5. Panel Administrativo y Reportes** | CU-16 | Procesar admisión 1ª opción | `admision.index` | `procesar admision` |
| | CU-17 | Reasignar a 2ª opción | `admision.index` | `procesar admision` |
| | CU-18 | Publicar resultado final | `admision.index` | `publicar admision` |
| | CU-19 | Reportes y estadísticas | — | **Próximamente** (todos) |

> **Decisión de unificación:** CU-08 se controla con `ver cupos` (no `ver carreras`). El "Proceso de Admisión" del sidebar se controla con `procesar admision` (igual que el dashboard), para que Docente NO lo vea como activo.

---

## Matriz de acceso por rol (la "mejor opción")

Permisos actuales en `RolesSeeder.php`:
- **Administrador del Sistema:** todos.
- **Docente:** `ver grupos`, `ver postulantes`, `ver notas`, `crear notas`, `editar notas`, `ver admision`.
- **Postulante:** `ver postulantes`.

Resultado de UI esperado por CU (✅ enlace activo · 🔒 "Sin acceso" · 🌐 público · ⏳ Próximamente):

| CU | Administrador | Docente | Postulante |
|---|---|---|---|
| CU-01/02/03 | 🌐 | 🌐 | 🌐 |
| CU-04 usuarios/roles/bitácora | ✅ | 🔒 | 🔒 |
| CU-05 postulantes | ✅ | ✅ | 🔒 |
| CU-06 gestiones | ✅ | 🔒 | 🔒 |
| CU-07 carreras | ✅ | 🔒 | 🔒 |
| CU-08 cupos | ✅ | 🔒 | 🔒 |
| CU-09 materias | ✅ | 🔒 | 🔒 |
| CU-10 docentes | ✅ | 🔒 | 🔒 |
| CU-11 grupos | ✅ | ✅ | 🔒 |
| CU-12 asignar docente | ✅ | 🔒 | 🔒 |
| CU-13/14/15 notas | ✅ | ✅ | 🔒 |
| CU-16/17 admisión | ✅ | 🔒 | 🔒 |
| CU-18 publicar | ✅ | 🔒 | 🔒 |
| CU-19 reportes | ⏳ | ⏳ | ⏳ |
| CU-20 pasarela | ⏳ | ⏳ | ⏳ |

> Nota: con la matriz anterior y los permisos actuales, **el Postulante verá casi todo como "Sin acceso"** dentro del panel administrativo (correcto según el pedido). Docente tendrá activos: postulantes (lectura), grupos (lectura) y notas. Si quieres que el Docente NO vea postulantes, quita `ver postulantes` del rol Docente en `RolesSeeder.php` (cambio opcional, marcado abajo).

---

## Tareas concretas

### Tarea 1 — Reescribir el sidebar a 5 módulos (`resources/views/layouts/ap.blade.php`)

Reemplazar todo el bloque `<nav class="cup-sb">` por **5 secciones** (`<div class="sb-sec">`) con estos títulos exactos, en este orden:

1. `🔐 Autenticación y Seguridad`
2. `👤 Registro de Postulantes y Gestión Académica`
3. `🏫 Asignación de Grupos y Docentes`
4. `📝 Exámenes y Control Académico`
5. `📊 Panel Administrativo y Reportes`

Reglas para cada ítem navegable:
- Mantener "Panel de Control" como primer enlace dentro del Módulo 1.
- Para cada CU con permiso, usar el patrón **activo / Sin acceso** (mismo de los demás ítems):

```blade
@can('ver gestiones')
  <a class="ni {{ request()->routeIs('gestiones.*') ? 'act':'' }}" href="{{ route('gestiones.index') }}">
    <i class="ico fas fa-calendar-alt"></i>Gestiones Académicas</a>
@else
  <span class="ni pnd"><i class="ico fas fa-calendar-alt"></i>Gestiones Académicas<span class="nbg">Sin acceso</span></span>
@endcan
```

- En el Módulo 2 del sidebar deben aparecer **los 5 CU** (Postulantes, Gestiones, Carreras y Cupos, Cupos por Gestión, Materias del CUP), cada uno con su `@else` "Sin acceso". CU-20 (pasarela) opcional como `<span class="ni pnd">...<span class="nbg">Próximamente</span></span>`.
- En el Módulo 3 mostrar Docentes + Grupos y Horarios (cada uno con su `@else`).
- En el Módulo 4 mostrar Registro de Notas (con `@else`).
- En el Módulo 5 mostrar "Proceso de Admisión" gated por `@can('procesar admision')` (con `@else` "Sin acceso") + "Reportes y Estadísticas" como `Próximamente`.
- Eliminar el `@can('ver carreras')` que envuelve a la vez Carreras **y** Cupos sin `@else` para Cupos: separar para que **Cupos** tenga su propio `@can('ver cupos') / @else`.

### Tarea 2 — Corregir el dashboard (`resources/views/panel/index.blade.php`)

Para **cada CU navegable** que hoy usa solo `@can ... @endcan`, agregar el `@else` con el estado deshabilitado, replicando el patrón ya usado en CU-05:

```blade
@can('ver gestiones')
  <div class="cr2x lnk"><a href="{{ route('gestiones.index') }}"><span class="ctg dn">CU-06</span><i class="ci2 fas fa-calendar-alt"></i>Gestionar gestiones académicas</a></div>
@else
  <div class="cr2x dis"><span class="ctg pn">CU-06</span><i class="ci2 fas fa-lock"></i>Gestionar gestiones académicas<span class="cpl">Sin acceso</span></div>
@endcan
```

Aplicarlo a: CU-04, CU-06, CU-07, CU-08, CU-09, CU-10, CU-11, CU-12, CU-13, CU-14, CU-15, CU-16, CU-17, CU-18.
- CU-08: cambiar el gate a `@can('ver cupos')`.
- CU-01/02/03 quedan como enlaces públicos (sin gate).
- CU-19 y CU-20 quedan como `Próximamente`.

### Tarea 3 — Coherencia de permisos en seeders

En `database/seeders/PermissionSeeder.php` confirmar que existen (ya están): `ver cupos`, `procesar admision`, `publicar admision`. No falta ninguno.

En `database/seeders/RolesSeeder.php` dejar la matriz coherente:
- **Administrador del Sistema:** `Permission::all()` (sin cambios).
- **Docente:** `['ver grupos','ver notas','crear notas','editar notas']`
  - *(Opcional — decisión del usuario)* añadir `'ver postulantes'` si el docente debe ver postulantes. Quitar `'ver admision'` (no debe ver admisión según la matriz).
- **Postulante:** `[]` (vacío) para que TODO el panel admin le aparezca como "Sin acceso".
  - *(Opcional)* si el postulante debe ver solo su propio registro, ese es un flujo aparte, no el CRUD de `ver postulantes`.

> Si cambias seeders, hay que recargar: `php artisan migrate:fresh --seed` (cuidado: borra datos). Alternativa sin borrar: crear un comando/tinker que haga `syncPermissions` sobre los roles existentes.

---

## Verificación (criterios de aceptación)

Levantar la app y probar con los 3 usuarios sembrados (password `12345678`):
`admin@cup.edu.bo`, `docente@cup.edu.bo`, `postulante@cup.edu.bo`.

- [ ] El **sidebar muestra exactamente 5 módulos**, en el mismo orden y con los mismos títulos que el dashboard.
- [ ] Para **cada rol**, todos los módulos y todos los CU **siempre se ven**; ninguno desaparece.
- [ ] Los CU sin permiso aparecen **deshabilitados con "Sin acceso"** (sidebar y dashboard), no como enlace.
- [ ] **Admin:** todos los CU activos (salvo Próximamente).
- [ ] **Docente:** activos solo Grupos y Notas; el resto "Sin acceso". (postulantes según decisión opcional)
- [ ] **Postulante:** todo el panel admin en "Sin acceso".
- [ ] CU-19 y CU-20 siguen como **Próximamente** para todos.
- [ ] No se rompe ninguna ruta; navegar a un CU activo carga su vista (HTTP 200).
- [ ] No se introdujo CSS nuevo: se reutilizan `ni pnd / nbg` y `cr2x dis / ctg pn / cpl`.

Comandos:
```bash
composer install && npm install
php artisan migrate:fresh --seed   # si tocaste seeders
npm run dev                        # o npm run build
php artisan serve                  # APP_URL :8002
./vendor/bin/pint                  # formatear PHP antes de commitear
```

---

## Pasos para Claude Code (orden de ejecución)

1. Abrir y releer `resources/views/panel/index.blade.php` y `resources/views/layouts/ap.blade.php` para confirmar el estado actual.
2. Editar el sidebar (Tarea 1) → 5 módulos con patrón Sin acceso en cada CU.
3. Editar el dashboard (Tarea 2) → agregar `@else` "Sin acceso" a todos los CU navegables.
4. Ajustar `RolesSeeder.php` según la matriz (Tarea 3) y recargar permisos.
5. Levantar la app y validar la matriz con los 3 usuarios.
6. Ejecutar `./vendor/bin/pint` y reportar qué quedó pendiente o qué decisiones opcionales tomaste.
