<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega las FK de users → docentes y users → postulantes.
 * Se ejecuta DESPUÉS de que ambas tablas ya existen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('docente_id')
                  ->references('id')->on('docentes')
                  ->nullOnDelete();

            $table->foreign('postulante_id')
                  ->references('id')->on('postulantes')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['docente_id']);
            $table->dropForeign(['postulante_id']);
        });
    }
};
