<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificacionTable extends Migration
{
    public function up()
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('contenido');
            $table->dateTime('fecha_hora');
            $table->enum('tipo', ['Urgente', 'Informativa', 'Recordatorio']);
            $table->foreignId('residente_id')->nullable()->constrained()->onDelete('cascade'); // <-- Ahora opcional
            $table->string('ruta')->nullable(); // <-- Ruta de redirección (opcional)
            $table->boolean('leida')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificacions');
    }
}
