<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Amplía la tabla bitacoras con todas las columnas necesarias para el CUP.
 * Convierte user_id en nullable (permite registrar incluso antes de autenticar).
 * Es IDEMPOTENTE: usa hasColumn() antes de cada cambio.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bitacoras', function (Blueprint $table) {

            // Columna usuario (nombre del usuario en texto, redundante con user_id
            // pero necesaria para conservar el nombre aunque el usuario se elimine)
            if (!Schema::hasColumn('bitacoras', 'usuario')) {
                $table->string('usuario', 120)->nullable()->after('user_id');
            }

            // Módulo del sistema al que pertenece la acción
            if (!Schema::hasColumn('bitacoras', 'modulo')) {
                $table->string('modulo', 60)->nullable()->after('usuario');
            }

            // Ampliar accion a 250 caracteres si era 100
            // (no se puede modificar tamaño directamente en SQLite, se hace en MySQL/PG)
            // Se omite para compatibilidad; el middleware trunca a 250 caracteres.

            // Método HTTP (GET, POST, PUT, DELETE, etc.)
            if (!Schema::hasColumn('bitacoras', 'metodo_http')) {
                $table->string('metodo_http', 10)->nullable()->after('modulo');
            }

            // Ruta de la petición
            if (!Schema::hasColumn('bitacoras', 'ruta')) {
                $table->string('ruta', 255)->nullable()->after('metodo_http');
            }

            // User-agent del navegador
            if (!Schema::hasColumn('bitacoras', 'user_agent')) {
                $table->string('user_agent', 255)->nullable()->after('ip');
            }

            // Descripción libre (para BitacoraTrait)
            if (!Schema::hasColumn('bitacoras', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('user_agent');
            }
        });

        // Hacer user_id nullable en MySQL/MariaDB
        // (Laravel no permite cambiar a nullable directamente en SQLite)
        try {
            $driver = DB::getDriverName();
            if (in_array($driver, ['mysql', 'mariadb', 'pgsql'])) {
                Schema::table('bitacoras', function (Blueprint $table) {
                    $table->foreignId('user_id')->nullable()->change();
                });
            }
        } catch (\Throwable $e) {
            // Si falla (SQLite, sin doctrine), no es crítico
        }
    }

    public function down(): void
    {
        Schema::table('bitacoras', function (Blueprint $table) {
            $cols = ['usuario', 'modulo', 'metodo_http', 'ruta', 'user_agent', 'descripcion'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('bitacoras', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
