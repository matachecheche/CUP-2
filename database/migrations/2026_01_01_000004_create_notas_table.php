<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CU-13 a CU-15: Notas (3 exámenes 30%+30%+40%, nota_final calculada)
return new class extends Migration {
    public function up(): void {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulante_id')->constrained()->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grupo_id')->constrained()->cascadeOnDelete();
            $table->decimal('examen1', 5, 2)->nullable();
            $table->decimal('examen2', 5, 2)->nullable();
            $table->decimal('examen3', 5, 2)->nullable();
            $table->decimal('nota_final', 5, 2)->nullable();
            $table->boolean('aprobado')->nullable();
            $table->unique(['postulante_id', 'materia_id', 'grupo_id']);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('notas'); }
};
