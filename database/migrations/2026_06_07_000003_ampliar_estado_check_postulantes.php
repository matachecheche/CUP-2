<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * CU-20: el módulo de pagos introdujo el estado 'preinscrito', pero el CHECK
 * que enum() generó en la migración original no lo incluía, provocando
 * SQLSTATE[23514] al registrar postulantes. Se amplía la restricción.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE postulantes DROP CONSTRAINT IF EXISTS postulantes_estado_check');
        DB::statement("ALTER TABLE postulantes ADD CONSTRAINT postulantes_estado_check
            CHECK (estado IN ('preinscrito','inscrito','en_curso','aprobado','no_aprobado','admitido','admitido_segunda_opcion','no_admitido'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE postulantes DROP CONSTRAINT IF EXISTS postulantes_estado_check');
        DB::statement("ALTER TABLE postulantes ADD CONSTRAINT postulantes_estado_check
            CHECK (estado IN ('inscrito','en_curso','aprobado','no_aprobado','admitido','admitido_segunda_opcion','no_admitido'))");
    }
};
