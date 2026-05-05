<?php

use App\Models\Mantenimiento;
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
        Schema::create('area_comuns', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->decimal('monto',10,2);
            $table->enum('estado', ['activo', 'inactivo', 'mantenimiento'])->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_comuns');
    }
};
