<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->string('ci',20)->unique();
            $table->string('nombres',100);
            $table->string('apellidos',100);
            $table->string('telefono',20)->nullable();
            $table->string('email',100)->unique();
            $table->string('titulo_profesional',150);
            $table->string('maestria',150);
            $table->string('diplomado_educacion_superior',150);
            $table->string('certificacion_ingles',100)->nullable();
            $table->text('otras_certificaciones')->nullable();
            $table->string('area_formacion',80)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('docentes'); }
};
