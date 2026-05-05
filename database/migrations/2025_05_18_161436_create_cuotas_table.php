<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['pendiente', 'activa', 'cancelada', 'pagado'])->default('pendiente');
            $table->foreignId('tipo_cuota_id')->nullable()->constrained('tipos_cuotas')->onDelete('set null');
            $table->foreignId('residente_id')->nullable()->constrained()->onDelete('set null'); // si se asigna a un solo residente
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // para saber quién emitió la cuota
            $table->text('observacion')->nullable(); // comentarios opcionales
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuotas');
    }
};
