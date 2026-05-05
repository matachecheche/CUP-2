<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_cuotas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: Agua, Mantenimiento
            $table->string('frecuencia'); // mensual, anual, puntual
            $table->boolean('editable')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_cuotas');
    }
};
