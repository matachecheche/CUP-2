<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CU-11: Definir cupos por carrera y gestión
return new class extends Migration {
    public function up(): void {
        Schema::create('cupos_carrera', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrera_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gestion_id')->constrained('gestiones')->cascadeOnDelete();
            $table->unsignedInteger('cantidad_maxima');
            $table->unique(['carrera_id', 'gestion_id']);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('cupos_carrera'); }
};
