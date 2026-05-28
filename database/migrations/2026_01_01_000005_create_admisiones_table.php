<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CU-16 a CU-18: Resultado final de admisión
return new class extends Migration {
    public function up(): void {
        Schema::create('admisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulante_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('gestion_id')->constrained('gestiones');
            $table->decimal('promedio_general', 5, 2)->nullable();
            $table->foreignId('carrera_asignada_id')->nullable()->constrained('carreras');
            $table->enum('resultado', [
                'pendiente', 'admitido_primera', 'admitido_segunda', 'no_admitido',
            ])->default('pendiente');
            $table->boolean('publicado')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('admisiones'); }
};
