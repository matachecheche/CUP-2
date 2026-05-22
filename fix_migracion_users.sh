#!/usr/bin/env bash
# =============================================================================
#  fix_migracion_users.sh
#  Corrige el error de FK en users → docentes/postulantes
#  Ejecutar desde la raíz del proyecto Laravel
# =============================================================================

set -e
GREEN='\033[0;32m'; CYAN='\033[0;36m'; NC='\033[0m'
info()    { echo -e "${CYAN}[INFO]${NC}  $1"; }
success() { echo -e "${GREEN}[OK]${NC}    $1"; }

# ── 1. Reescribir la migración de users SIN foreign keys ──────────────────────
info "Reescribiendo migración de users (sin FK a docentes/postulantes)..."

cat > database/migrations/0001_01_01_000013_create_users_table.php << 'MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla users sin FK a docentes/postulantes.
 * Las FK se agregan en 2026_01_01_000007_add_fk_users_table.php
 * una vez que esas tablas ya existen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Columnas sin FK por ahora (se agregan después)
            $table->unsignedBigInteger('docente_id')->nullable();
            $table->unsignedBigInteger('postulante_id')->nullable();

            $table->boolean('activo')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
MIGRATION
success "Migración users reescrita (sin FK)"

# ── 2. Crear migración que agrega las FK después de docentes y postulantes ────
info "Creando migración para FK diferidas..."

cat > database/migrations/2026_01_01_000007_add_fk_users_table.php << 'MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega las FK de users → docentes y users → postulantes.
 * Se ejecuta DESPUÉS de que ambas tablas ya existen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('docente_id')
                  ->references('id')->on('docentes')
                  ->nullOnDelete();

            $table->foreign('postulante_id')
                  ->references('id')->on('postulantes')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['docente_id']);
            $table->dropForeign(['postulante_id']);
        });
    }
};
MIGRATION
success "Migración FK diferida creada: 2026_01_01_000007_add_fk_users_table.php"

# ── 3. Limpiar caché ──────────────────────────────────────────────────────────
info "Limpiando caché..."
php artisan config:clear 2>/dev/null || true
php artisan migrate:clear 2>/dev/null || true

echo ""
echo -e "${GREEN}══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}  Fix aplicado. Ahora ejecuta:${NC}"
echo ""
echo "    php artisan migrate:fresh --seed"
echo ""
echo -e "${GREEN}══════════════════════════════════════════════════════════${NC}"
