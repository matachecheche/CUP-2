<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuota_id')->nullable()->constrained('cuotas')->onDelete('cascade');
            $table->foreignId('multa_id')->nullable()->constrained('multas')->onDelete('cascade');
            $table->decimal('monto_pagado', 10, 2);
            $table->date('fecha_pago');
            $table->string('metodo')->nullable(); // efectivo, QR, Stripe, etc.
            $table->string('estado')->default('pendiente'); // pendiente, aprobado, rechazado
            $table->string('comprobante')->nullable(); // imagen del comprobante QR
            $table->text('observacion')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pagos');
    }
};
