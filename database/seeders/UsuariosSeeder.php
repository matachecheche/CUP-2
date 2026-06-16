<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $pwd = Hash::make('12345678');

        // Un usuario de prueba por rol
        $usuarios = [
            [
                'name'  => 'Administrador CUP',
                'email' => 'admin@cup.edu.bo',
                'rol'   => 'Administrador del Sistema',
            ],
            [
                'name'  => 'Docente Demo',
                'email' => 'docente@cup.edu.bo',
                'rol'   => 'Docente',
            ],
            [
                'name'  => 'Coordinador CUP',
                'email' => 'coordinador@cup.edu.bo',
                'rol'   => 'Coordinador del CUP',
            ],
            [
                'name'  => 'Postulante Demo',
                'email' => 'postulante@cup.edu.bo',
                'rol'   => 'Postulante',
            ],
        ];

        foreach ($usuarios as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'activo'            => true,
                    'email_verified_at' => now(),
                    'password'          => $pwd,
                ]
            );
            $user->syncRoles([$data['rol']]);
        }
    }
}
