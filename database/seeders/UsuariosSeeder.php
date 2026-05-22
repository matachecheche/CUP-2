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

        $usuarios = [
            ['name' => 'admin',         'email' => 'admin@cup.edu.bo',      'rol' => 'Administrador'],
            ['name' => 'admisiones',    'email' => 'admisiones@cup.edu.bo', 'rol' => 'Responsable de Admisiones'],
            ['name' => 'docente',       'email' => 'docente@cup.edu.bo',    'rol' => 'Docente'],
            ['name' => 'autoridad',     'email' => 'autoridad@cup.edu.bo',  'rol' => 'Autoridad de la Facultad'],
        ];

        foreach ($usuarios as $data) {
            $user = User::create([
                'name'               => $data['name'],
                'email'              => $data['email'],
                'activo'             => true,
                'email_verified_at'  => now(),
                'password'           => $pwd,
            ]);
            $user->assignRole($data['rol']);
        }
    }
}
