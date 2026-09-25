<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <title>NexoVault · Bóveda privada</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('nexovault.css') }}">
</head>
<body class="login">
<div class="login-stage">
    <section class="login-intro">
        <div class="login-brand"><div class="vaultmark">N</div><div><b>NEXO<span>VAULT</span></b><small>PRIVATE CONTROL CENTER</small></div></div>
        <div class="login-copy">
            <span class="eyebrow">SEGURIDAD · PROYECTOS · CONTROL</span>
            <h1>Todo lo importante,<br><strong>bajo tu control.</strong></h1>
            <p>Centraliza proyectos, credenciales, URLs, hitos y próximos desarrollos en una bóveda privada.</p>
        </div>
        <div class="login-points">
            <span>✓ Credenciales cifradas</span><span>✓ Proyectos y avance</span><span>✓ Planificación técnica</span>
        </div>
    </section>

    <section class="login-card-wrap">
        <div class="loginbox">
            <div class="loginbox-head">
                <div class="vaultmark">N</div>
                <div><span class="eyebrow">NEXOVAULT</span><h2>{{ $configured ? 'Desbloquear bóveda' : 'Configuración inicial' }}</h2></div>
            </div>
            <p>{{ $configured ? 'Introduce tu contraseña maestra para acceder a tu centro privado.' : 'Inicializa la bóveda local de desarrollo.' }}</p>

            @if ($errors->any())
                <div class="err">{{ $errors->first() }}</div>
            @endif

            @if (!$configured)
                @if (app()->environment('local'))
                    <div class="dev-notice"><b>Entorno de desarrollo</b><span>Contraseña maestra temporal: <code>12345678</code></span></div>
                @endif
                <form method="POST" action="{{ route('vault.setup') }}">
                    @csrf
                    <label>Contraseña maestra
                        <input type="password" name="password" value="{{ app()->environment('local') ? '12345678' : '' }}" autocomplete="new-password" required>
                    </label>
                    <label>Confirmar contraseña
                        <input type="password" name="password_confirmation" value="{{ app()->environment('local') ? '12345678' : '' }}" autocomplete="new-password" required>
                    </label>
                    <button type="submit">Crear bóveda <span>→</span></button>
                </form>
            @else
                <form method="POST" action="{{ route('vault.unlock') }}">
                    @csrf
                    <label>Contraseña maestra
                        <input type="password" name="password" placeholder="Introduce tu contraseña" autocomplete="current-password" autofocus required>
                    </label>
                    <button type="submit">Desbloquear bóveda <span>→</span></button>
                </form>
            @endif

            <div class="privacy">🔒 La contraseña maestra no se almacena. Solo se utiliza para derivar la clave que protege tu bóveda.</div>
        </div>
    </section>
</div>
</body>
</html>
