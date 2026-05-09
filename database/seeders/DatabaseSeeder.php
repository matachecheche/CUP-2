<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RolesSeeder::class,
            UsuariosSeeder::class,
            ClasificadoresSeeder::class,
            CargoEmpleadosSeeder::class,
            EmpleadosSeeder::class,
            ResidentesSeeder::class,
            VisitasSeeder::class,
            TipoCuotaSeeder::class,
            CuotaSeeder::class,
            PagoSeeder::class,
            EmpresaExternaSeeder::class,
            MantenimientoSeeder::class,
            AreaComunSeeder::class,
            ReservaSeeder::class,
            MultaSeeder::class,
            ComunicadoSeeder::class,
        ]);
    }
}