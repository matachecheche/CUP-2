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

        // Proxy confiable (Codespaces / proxies inversos)
        $middleware->trustProxies(at: '*');

        // ── Spatie Laravel Permission — aliases ───────────────────────
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // ── Bitácora automática — se ejecuta en TODAS las rutas web ──
        // Debe ir DESPUÉS de StartSession/Auth para que Auth::check() funcione.
        // En Laravel 11 appendToGroup garantiza el orden correcto.
        $middleware->appendToGroup('web', \App\Http\Middleware\BitacoraMiddleware::class);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
