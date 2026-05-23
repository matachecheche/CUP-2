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
