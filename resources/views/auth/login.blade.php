<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar — Sistema de Admisión CUP</title>
    <link href="{{ asset('css/cup.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: var(--crema);
            background-image:
                repeating-linear-gradient(
                    0deg, transparent, transparent 38px,
                    rgba(26,58,42,.04) 38px, rgba(26,58,42,.04) 39px
                ),
                repeating-linear-gradient(
                    90deg, transparent, transparent 38px,
                    rgba(26,58,42,.04) 38px, rgba(26,58,42,.04) 39px
                );
        }
        .login-wrap {
            width: 100%; max-width: 420px; padding: 1.5rem;
        }
        .login-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }
        .login-head {
            background: var(--verde);
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            border-bottom: 4px solid var(--oro);
        }
        .login-head .escudo {
            width: 64px; height: 64px; border-radius: 50%;
            background: var(--oro);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto .9rem;
            font-size: 1.6rem; color: var(--verde); font-weight: 900;
        }
        .login-head h1 {
            font-family: var(--font-display);
            font-size: 1.5rem; font-weight: 700;
            color: var(--white); margin: 0;
        }
        .login-head p {
            font-size: .8rem; color: rgba(255,255,255,.6);
            margin-top: .25rem;
        }
        .login-body { padding: 1.75rem 2rem 2rem; }
        .field { margin-bottom: 1rem; }
        .input-wrap {
            position: relative;
        }
        .input-wrap .inp-icon {
            position: absolute; left: .8rem; top: 50%;
            transform: translateY(-50%);
            color: var(--txt-3); font-size: .88rem;
        }
        .input-wrap .form-control {
            padding-left: 2.4rem;
        }
        .btn-login {
            width: 100%;
            background: var(--verde);
            color: var(--white);
            border: none; border-radius: 7px;
            padding: .75rem;
            font-family: var(--font-body);
            font-size: .95rem; font-weight: 700;
            cursor: pointer; transition: .2s;
            display: flex; align-items: center; justify-content: center; gap: .5rem;
        }
        .btn-login:hover { background: var(--verde-2); transform: translateY(-1px); box-shadow: var(--shadow); }
        .forgot { text-align: center; margin-top: 1rem; }
        .forgot a { font-size: .82rem; color: var(--txt-3); }
        .forgot a:hover { color: var(--verde); }
        .login-footer {
            background: var(--crema); border-top: 1px solid var(--border);
            text-align: center; padding: .6rem;
            font-size: .7rem; color: var(--txt-3);
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-head">
            <div class="escudo">C</div>
            <h1>Sistema de Admisión CUP</h1>
            <p>Curso Preuniversitario — Facultad de Ingeniería</p>
        </div>

        <div class="login-body">
            @if($errors->any())
            @foreach($errors->all() as $err)
            <div class="alert alert-danger" style="margin-bottom:.75rem;">
                <i class="fas fa-exclamation-circle"></i> {{ $err }}
            </div>
            @endforeach
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="field">
                    <label class="form-label" for="email">Correo institucional</label>
                    <div class="input-wrap">
                        <i class="inp-icon fas fa-envelope"></i>
                        <input id="email" type="email" name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            placeholder="usuario@cup.edu.bo"
                            autocomplete="email" autofocus>
                    </div>
                </div>

                <div class="field">
                    <label class="form-label" for="password">Contraseña</label>
                    <div class="input-wrap">
                        <i class="inp-icon fas fa-lock"></i>
                        <input id="password" type="password" name="password"
                            class="form-control"
                            placeholder="••••••••"
                            autocomplete="current-password">
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Ingresar al Sistema
                </button>

                <div class="forgot">
                    <a href="{{ route('password.request') }}">
                        <i class="fas fa-key" style="margin-right:.3rem;"></i>
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
            </form>
        </div>

        <div class="login-footer">
            © {{ date('Y') }} CUP — Todos los derechos reservados
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
