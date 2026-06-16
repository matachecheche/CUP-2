<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Flujo de recuperación de contraseña (fix de imports en ForgotPassword/ResetPassword)
 * y política de contraseña segura en la gestión de usuarios.
 * Corre contra la BD de desarrollo: DatabaseTransactions revierte todo al final.
 */
class PasswordResetFlowTest extends TestCase
{
    use DatabaseTransactions;

    private function usuarioDePrueba(): User
    {
        return User::create([
            'name' => 'Usuario Prueba Reset',
            'email' => 'prueba.reset.'.uniqid().'@example.test',
            'password' => 'ClaveInicial#123',
            'activo' => true,
        ]);
    }

    public function test_formulario_de_solicitud_carga(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    public function test_solicitar_enlace_envia_notificacion(): void
    {
        Notification::fake();
        $user = $this->usuarioDePrueba();

        $respuesta = $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => $user->email]);

        $respuesta->assertRedirect(route('password.request'));
        $respuesta->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_con_token_valido_cambia_la_contrasena(): void
    {
        $user = $this->usuarioDePrueba();
        $token = Password::broker()->createToken($user);

        $respuesta = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NuevaClave#456',
            'password_confirmation' => 'NuevaClave#456',
        ]);

        $respuesta->assertRedirect(route('panel'));
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('NuevaClave#456', $user->fresh()->password));
    }

    public function test_reset_con_token_invalido_no_cambia_la_contrasena(): void
    {
        $user = $this->usuarioDePrueba();

        $respuesta = $this->from(route('password.reset', 'token-invalido'))
            ->post(route('password.update'), [
                'token' => 'token-invalido',
                'email' => $user->email,
                'password' => 'NuevaClave#456',
                'password_confirmation' => 'NuevaClave#456',
            ]);

        $respuesta->assertRedirect(route('password.reset', 'token-invalido'));
        $respuesta->assertSessionHasErrors(['email']);
        $this->assertFalse(Hash::check('NuevaClave#456', $user->fresh()->password));
    }

    public function test_reset_rechaza_contrasena_debil(): void
    {
        $user = $this->usuarioDePrueba();
        $token = Password::broker()->createToken($user);

        $respuesta = $this->from(route('password.reset', $token))
            ->post(route('password.update'), [
                'token' => $token,
                'email' => $user->email,
                'password' => 'abc123', // 6 caracteres, sin mayúscula ni símbolo
                'password_confirmation' => 'abc123',
            ]);

        $respuesta->assertSessionHasErrors(['password']);
        $this->assertFalse(Hash::check('abc123', $user->fresh()->password));
    }

    public function test_actualizar_usuario_muestra_error_de_contrasena_debil(): void
    {
        $admin = User::where('email', 'admin@cup.edu.bo')->first();
        if (! $admin) {
            $this->markTestSkipped('No existe el usuario admin sembrado.');
        }
        $user = $this->usuarioDePrueba();

        $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'password' => '12345678', // sin mayúscula, número solo
                'password_confirmation' => '12345678',
                'role' => 'Administrador del Sistema',
            ])
            ->assertSessionHasErrors(['password']); // antes el catch genérico lo ocultaba
    }

    public function test_crear_usuario_rechaza_contrasena_debil(): void
    {
        $admin = User::where('email', 'admin@cup.edu.bo')->first();
        if (! $admin) {
            $this->markTestSkipped('No existe el usuario admin sembrado.');
        }

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Usuario Debil',
                'email' => 'debil.'.uniqid().'@example.test',
                'password' => 'password123', // sin mayúscula ni símbolo
                'password_confirmation' => 'password123',
                'role' => 'Administrador del Sistema',
            ])
            ->assertSessionHasErrors(['password']);
    }

    public function test_crear_usuario_acepta_contrasena_fuerte(): void
    {
        $admin = User::where('email', 'admin@cup.edu.bo')->first();
        if (! $admin) {
            $this->markTestSkipped('No existe el usuario admin sembrado.');
        }

        $email = 'fuerte.'.uniqid().'@example.test';
        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Usuario Fuerte',
                'email' => $email,
                'password' => 'Fuerte#2026',
                'password_confirmation' => 'Fuerte#2026',
                'role' => 'Administrador del Sistema',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['email' => $email]);
    }
}
