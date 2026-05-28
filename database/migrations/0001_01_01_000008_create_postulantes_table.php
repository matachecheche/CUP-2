<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('postulantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gestion_id')->constrained('gestiones');
            $table->foreignId('primera_opcion_id')->constrained('carreras');
            $table->foreignId('segunda_opcion_id')->constrained('carreras');
            $table->string('ci',20)->unique();
            $table->string('nombres',100);
            $table->string('apellidos',100);
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo',['M','F','Otro'])->nullable();
            $table->string('direccion',200)->nullable();
            $table->string('telefono',20)->nullable();
            $table->string('email',100)->unique();
            $table->string('colegio_procedencia',150)->nullable();
            $table->string('ciudad',80)->nullable();
            $table->boolean('doc_ci')->default(false);
            $table->boolean('doc_libreta_colegio')->default(false);
            $table->boolean('doc_titulo_bachiller')->default(false);
            $table->enum('estado',['inscrito','en_curso','aprobado','no_aprobado','admitido','admitido_segunda_opcion','no_admitido'])->default('inscrito');
            $table->decimal('promedio_general',5,2)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('postulantes'); }
};
