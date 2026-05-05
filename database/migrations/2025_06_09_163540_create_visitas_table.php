<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('residente_id')->constrained('residentes')->onDelete('cascade');
            $table->string('nombre_visitante');
            $table->string('ci_visitante', 20);
            $table->string('placa_vehiculo', 20)->nullable();
            $table->string('motivo');
            $table->datetime('fecha_inicio');
            $table->datetime('fecha_fin');
            $table->string('codigo', 6)->unique();
            $table->enum('estado', ['pendiente', 'en_curso', 'finalizada', 'rechazada'])->default('pendiente');
            $table->datetime('hora_entrada')->nullable();
            $table->datetime('hora_salida')->nullable();
            $table->foreignId('user_entrada_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('user_salida_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Índices para búsquedas frecuentes
            $table->index(['codigo', 'ci_visitante']);
            $table->index('estado');
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};