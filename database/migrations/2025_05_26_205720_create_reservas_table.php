<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('estado', 20)->default('pendiente'); // pendiente, confirmada, cancelada
            $table->string('observacion')->nullable();
            $table->decimal('monto_total', 10, 2);
            // Foreign keys
            $table->foreignId('area_comun_id')
                ->constrained('area_comuns')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('residente_id')
                ->constrained('residentes')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
