#!/usr/bin/env bash
# =============================================================================
#  fix_middleware_spatie.sh
#  Registra los middlewares de Spatie Permission en bootstrap/app.php
#  (Laravel 11 no los registra automáticamente)
#  Ejecutar desde la raíz del proyecto Laravel
# =============================================================================

set -e
GREEN='\033[0;32m'; CYAN='\033[0;36m'; NC='\033[0m'
info()    { echo -e "${CYAN}[INFO]${NC}  $1"; }
success() { echo -e "${GREEN}[OK]${NC}    $1"; }

info "Actualizando bootstrap/app.php con middlewares de Spatie..."

cat > bootstrap/app.php << 'PHP'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Proxy confiable (necesario para Codespaces / proxies inversos)
        $middleware->trustProxies(at: '*');

        // ── Spatie Laravel Permission — aliases de middleware ─────────────
        // Sin esto Laravel 11 no reconoce 'role:', 'permission:' ni 'role_or_permission:'
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
PHP

success "bootstrap/app.php actualizado"

info "Limpiando caché de configuración y rutas..."
php artisan config:clear  2>/dev/null || true
php artisan route:clear   2>/dev/null || true
php artisan cache:clear   2>/dev/null || true

echo ""
echo -e "${GREEN}══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}  Listo. Los middlewares 'permission:' y 'role:' ya        ${NC}"
echo -e "${GREEN}  funcionan. Recarga /users en el navegador.               ${NC}"
echo -e "${GREEN}══════════════════════════════════════════════════════════${NC}"
