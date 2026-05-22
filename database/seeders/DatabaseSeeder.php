<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Seguridad (orden importa: permisos → roles → usuarios)
            PermissionSeeder::class,
            RolesSeeder::class,
            UsuariosSeeder::class,

            // Datos de referencia del dominio CUP
            // (descomenta a medida que implementes los módulos)
            // GestionSeeder::class,
            // CarreraSeeder::class,
            // MateriaSeeder::class,
            // DocenteSeeder::class,
        ]);
    }
}
