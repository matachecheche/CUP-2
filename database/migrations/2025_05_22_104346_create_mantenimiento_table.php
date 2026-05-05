<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->integer('estado');
            $table->dateTime('fecha_hora');
            $table->decimal('monto', 10, 2);

            // Clave foránea con usuario
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');

            // Clave foránea con empresa externa (nombre exacto de la tabla: empresa_externas)
            $table->unsignedBigInteger('empresaExterna_id')->nullable();
            $table->foreign('empresaExterna_id')
                  ->references('id')
                  ->on('empresa_externas') // ← nombre exacto de la tabla
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};