<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Iniciar Sesión — CUP</title>
<link href="{{ asset('css/cup.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
body{min-height:100vh;display:grid;place-items:center;background:var(--cr);
  background-image:repeating-linear-gradient(0deg,transparent,transparent 38px,rgba(26,58,42,.04) 38px,rgba(26,58,42,.04) 39px),
    repeating-linear-gradient(90deg,transparent,transparent 38px,rgba(26,58,42,.04) 38px,rgba(26,58,42,.04) 39px)}
.lw{width:100%;max-width:420px;padding:1.5rem}
.lc{background:var(--w);border:1px solid var(--b);border-radius:12px;box-shadow:var(--sl);overflow:hidden}
.lh{background:var(--v);padding:2rem 2rem 1.5rem;text-align:center;border-bottom:4px solid var(--o)}
.esc{width:64px;height:64px;border-radius:50%;background:var(--o);display:flex;align-items:center;
  justify-content:center;margin:0 auto .9rem;font-size:1.6rem;color:var(--v);font-weight:900}
.lh h1{font-family:var(--fd);font-size:1.5rem;font-weight:700;color:var(--w);margin:0}
.lh p{font-size:.8rem;color:rgba(255,255,255,.6);margin-top:.25rem}
.lb{padding:1.75rem 2rem 2rem}
.iw{position:relative}
.iw .ii{position:absolute;left:.8rem;top:50%;transform:translateY(-50%);color:var(--t3);font-size:.88rem}
.iw .fc{padding-left:2.4rem}
.btn-lg{width:100%;background:var(--v);color:var(--w);border:none;border-radius:7px;
  padding:.75rem;font-family:var(--fb);font-size:.95rem;font-weight:700;cursor:pointer;
  transition:.2s;display:flex;align-items:center;justify-content:center;gap:.5rem}
.btn-lg:hover{background:var(--v2);transform:translateY(-1px);box-shadow:var(--s)}
.fld{margin-bottom:1rem}
.fgt{text-align:center;margin-top:1rem;font-size:.82rem}
.fgt a{color:var(--t3)}
.fgt a:hover{color:var(--v)}
.lf{background:var(--cr);border-top:1px solid var(--b);text-align:center;padding:.6rem;font-size:.7rem;color:var(--t3)}
</style>
</head>
<body>
<div class="lw">
  <div class="lc">
    <div class="lh">
      <div class="esc">C</div>
      <h1>Sistema de Admisión CUP</h1>
      <p>Curso Preuniversitario — FICCT</p>
    </div>
    <div class="lb">
      @if($errors->any())
      @foreach($errors->all() as $err)
      <div class="al al-d" style="margin-bottom:.75rem"><i class="fas fa-exclamation-circle"></i> {{ $err }}</div>
      @endforeach
      @endif
      <form action="/login" method="POST" novalidate>
        @csrf
        <div class="fld">
          <label class="fl" for="email">Correo institucional</label>
          <div class="iw">
            <i class="ii fas fa-envelope"></i>
            <input id="email" type="email" name="email"
                   class="fc @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required maxlength="100"
                   placeholder="usuario@cup.edu.bo" autocomplete="email" autofocus>
          </div>
          @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="fld">
          <label class="fl" for="password">Contraseña</label>
          <div class="iw">
            <i class="ii fas fa-lock"></i>
            <input id="password" type="password" name="password"
                   class="fc @error('password') is-invalid @enderror"
                   required minlength="8"
                   placeholder="••••••••" autocomplete="current-password">
          </div>
          @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn-lg"><i class="fas fa-sign-in-alt"></i> Ingresar al Sistema</button>
        <div class="fgt"><a href="{{ route('password.request') }}"><i class="fas fa-key"></i> ¿Olvidaste tu contraseña?</a></div>
      </form>
    </div>
    <div class="lf">© {{ date('Y') }} CUP — Todos los derechos reservados</div>
  </div>
</div>
</body>
</html>
