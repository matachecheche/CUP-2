<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla users sin FK a docentes/postulantes.
 * Las FK se agregan en 2026_01_01_000007_add_fk_users_table.php
 * una vez que esas tablas ya existen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Columnas sin FK por ahora (se agregan después)
            $table->unsignedBigInteger('docente_id')->nullable();
            $table->unsignedBigInteger('postulante_id')->nullable();

            $table->boolean('activo')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
