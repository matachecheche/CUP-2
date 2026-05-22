<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iniciar sesión — Sistema de Admisión CUP</title>

    <link href="{{ asset('css/plantilla.css') }}" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #0f172a, #1e3a5f);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }

        .card-login {
            border-radius: 20px;
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            color: #f1f5f9;
        }

        .login-logo {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.6rem;
            box-shadow: 0 4px 20px rgba(37,99,235,0.5);
        }

        .card-login h4 {
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .card-login .subtitle {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.55);
            margin-top: -4px;
        }

        .input-group-text {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-right: none;
            color: #94a3b8;
            border-radius: 10px 0 0 10px;
        }

        .form-control {
            background: rgba(255,255,255,0.08) !important;
            border: 1px solid rgba(255,255,255,0.15);
            border-left: none;
            color: #f1f5f9 !important;
            border-radius: 0 10px 10px 0 !important;
            padding: 11px 14px;
        }

        .form-control::placeholder { color: rgba(255,255,255,0.35); }
        .form-control:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(14,165,233,0.45);
            border-color: rgba(14,165,233,0.5);
        }

        .btn-login {
            border-radius: 10px;
            padding: 11px;
            font-weight: 700;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            border: none;
            color: #fff;
            transition: 0.25s;
            letter-spacing: 0.3px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14,165,233,0.45);
        }

        .forgot-link {
            color: rgba(255,255,255,0.55);
            font-size: 0.83rem;
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: #38bdf8; text-decoration: underline; }

        .faculty-badge {
            display: inline-block;
            font-size: 0.68rem;
            color: rgba(255,255,255,0.35);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 2px 10px;
            margin-top: 6px;
        }
    </style>
</head>

<body>
<div class="login-wrapper">
    <div class="card-login p-4">

        <div class="text-center mb-4">
            <div class="login-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h4>Sistema de Admisión CUP</h4>
            <p class="subtitle">Curso Preuniversitario — Acceso al sistema</p>
            <span class="faculty-badge">Facultad de Ingeniería</span>
        </div>

        @if ($errors->any())
            @foreach ($errors->all() as $item)
                <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ $item }}
                    <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="alert"></button>
                </div>
            @endforeach
        @endif

        <form action="/login" method="POST">
            @csrf

            <div class="input-group mb-3">
                <span class="input-group-text">
                    <i class="fas fa-envelope fa-sm"></i>
                </span>
                <input type="email" name="email"
                    class="form-control"
                    placeholder="Correo institucional"
                    value="{{ old('email') }}"
                    autocomplete="email" autofocus>
            </div>

            <div class="input-group mb-4">
                <span class="input-group-text">
                    <i class="fas fa-lock fa-sm"></i>
                </span>
                <input type="password" name="password"
                    class="form-control"
                    placeholder="Contraseña"
                    autocomplete="current-password">
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('password.request') }}" class="forgot-link">
                    <i class="fas fa-key me-1"></i> ¿Olvidaste tu contraseña?
                </a>
            </div>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
