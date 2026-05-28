<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CU-12: Asignación docente-grupo-materia + horario
return new class extends Migration {
    public function up(): void {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('docente_id')->constrained()->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained()->cascadeOnDelete();
            $table->enum('dia', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado']);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('aula',30)->nullable();
            $table->unique(['grupo_id', 'materia_id']);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('asignaciones'); }
};
