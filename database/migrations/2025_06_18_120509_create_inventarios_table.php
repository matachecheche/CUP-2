<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('estado')->default('disponible'); // disponible,en_uso, mantenimiento, prestado, extraviado, baja
            $table->date('fecha_adquisicion')->nullable();
            $table->string('tipo_adquisicion')->nullable(); // compra, donación, leasing
            $table->decimal('valor_estimado', 10, 2)->nullable();
            $table->integer('vida_util')->nullable();
            $table->decimal('valor_residual', 10, 2)->nullable();
            $table->date('fecha_baja')->nullable();
            $table->string('motivo_baja')->nullable();
            $table->string('ubicacion')->nullable();

            $table->foreignId('categoria_id')->constrained('categoria_inventarios')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('area_comun_id')->nullable()->constrained('area_comuns')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
