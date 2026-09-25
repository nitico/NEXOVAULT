<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <title>@yield('title', 'NexoVault') · NexoVault</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('nexovault.css') }}">
</head>
<body>
<div class="shell">
    <aside>
        <div class="brand"><b>N</b><span>NEXO<strong>VAULT</strong><small>Private Control Center</small></span></div>
        <nav>
            <a href="{{ route('dashboard') }}">⌂ <span>Dashboard</span></a>
            <a href="{{ route('search') }}">⌕ <span>Búsqueda global</span></a>
            <a href="{{ route('projects.index') }}">▣ <span>Proyectos</span></a>
            <a href="{{ route('credentials.index') }}">◆ <span>Credenciales</span></a>
            <a href="{{ route('urls.index') }}">↗ <span>URLs y recursos</span></a>
            <a href="{{ route('plans.index') }}">✓ <span>Planificación</span></a>
            <a href="{{ route('security.index') }}">⚙ <span>Seguridad</span></a>
            <a href="{{ route('backup.index') }}">⇩ <span>Backup y restauración</span></a>
        </nav>
        <div class="sidebar-foot">
            <small>SESIÓN SEGURA</small>
            <form method="POST" action="{{ route('vault.lock') }}">@csrf<button class="lock">🔒 Bloquear bóveda</button></form>
        </div>
    </aside>
    <main>
        <header>
            <div><small>BÓVEDA PRIVADA</small><h1>@yield('title', 'NexoVault')</h1></div>
            <span class="secure">● Bóveda desbloqueada</span>
        </header>
        @if (session('ok'))<div class="ok">{{ session('ok') }}</div>@endif
        @if ($errors->any())<div class="err">{{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
</div>
</body>
</html>
