<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CU-11: Grupos (máx. 60 alumnos) + tabla pivote grupo_postulante
return new class extends Migration {
    public function up(): void {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gestion_id')->constrained('gestiones');
            $table->string('codigo', 20)->unique();
            $table->enum('turno', ['mañana', 'tarde', 'noche']);
            $table->enum('modalidad', ['presencial', 'virtual'])->default('presencial');
            $table->unsignedInteger('capacidad_maxima')->default(60);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('grupo_postulante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('postulante_id')->constrained()->cascadeOnDelete();
            $table->unique(['grupo_id', 'postulante_id']);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('grupo_postulante');
        Schema::dropIfExists('grupos');
    }
};
