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
        Schema::create('multas', function (Blueprint $table) {
            $table->id();
            $table->string('motivo', 255);
            $table->decimal('monto', 10, 2);
            $table->date('fechaEmision');
            $table->date('fechaLimite');
            $table->enum('estado', ['pendiente','pagada','anulada'])->default('pendiente');
            // Foreign keys
            $table->foreignId('residente_id')->nullable()
                ->constrained('residentes')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreignId('empleado_id')->nullable()
                ->constrained('empleados')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreignId('cuota_id')->nullable()
                ->constrained('cuotas')
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
        Schema::dropIfExists('multas');
    }
};
