@extends('layouts.app')
@section('title','Seguridad')
@section('content')
<div class="grid2">
<section class="panel">
<h2>Microsoft Authenticator</h2>
<p class="muted">Segundo factor TOTP compatible con Microsoft Authenticator. NexoVault no envía el secreto a servicios externos.</p>
@if($mfaEnabled)
<div class="ok">MFA está activado.</div>
<h3>Regenerar códigos de recuperación</h3>
<p class="muted">Requiere contraseña maestra y un código actual de Authenticator. Los códigos anteriores quedarán invalidados.</p>
<form method="POST" action="{{route('security.mfa.recovery.regenerate')}}">@csrf
<label>Contraseña maestra<input type="password" name="current_password" required autocomplete="current-password"></label>
<label>Código de Microsoft Authenticator<input name="mfa_code" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" required autocomplete="one-time-code"></label>
<button class="btn ghost">Generar nuevos códigos</button></form>
<hr style="border-color:#1b303d;margin:25px 0">
<h3>Desactivar MFA</h3><p class="muted">Acción sensible: requiere reautenticación completa.</p>
<form method="POST" action="{{route('security.mfa.disable')}}">@csrf
<label>Contraseña maestra<input type="password" name="current_password" required autocomplete="current-password"></label>
<label>Código de Microsoft Authenticator<input name="mfa_code" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" required autocomplete="one-time-code"></label>
<label>Escribe DESACTIVAR para confirmar<input name="confirm" required autocomplete="off"></label>
<button class="btn ghost" onclick="return confirm('¿Desactivar Microsoft Authenticator en NexoVault?')">Desactivar MFA</button></form>
@elseif(!$setupSecret)
<form method="POST" action="{{route('security.mfa.begin')}}">@csrf<button class="btn">Configurar Authenticator</button></form>
@else
<div class="dev-notice"><b>Agregar cuenta en Microsoft Authenticator</b><span>Clave secreta temporal: <code>{{$setupSecret}}</code></span><span>Esta configuración vence en 10 minutos.</span></div>
<p class="muted">Microsoft Authenticator → + → Otra cuenta → introducir clave manualmente. Nombre: NexoVault. Tipo: basado en tiempo.</p>
<form method="POST" action="{{route('security.mfa.confirm')}}">@csrf<label>Código de 6 dígitos<input name="code" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" autocomplete="one-time-code" required></label><button class="btn">Confirmar MFA</button></form>
<form method="POST" action="{{route('security.mfa.cancel')}}" style="margin-top:10px">@csrf<button class="btn ghost">Cancelar configuración</button></form>
@endif
</section>
<section class="panel"><h2>Contraseña maestra</h2><p class="muted">El cambio vuelve a proteger la clave de datos; no almacena la contraseña maestra.</p><form method="POST" action="{{route('security.master.change')}}" class="form" style="grid-template-columns:1fr">@csrf<label>Actual<input type="password" name="current_password" required autocomplete="current-password"></label><label>Nueva<input type="password" name="password" required autocomplete="new-password"></label><label>Confirmar nueva<input type="password" name="password_confirmation" required autocomplete="new-password"></label>@if($mfaEnabled)<label>Código de Microsoft Authenticator<input name="mfa_code" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" required autocomplete="one-time-code"></label>@endif<button class="btn" onclick="return confirm('¿Cambiar la contraseña maestra de NexoVault?')">Cambiar contraseña</button></form><p class="muted">Bloqueo automático: {{round($timeout/60)}} minutos de inactividad.</p></section></div>
<section class="panel" style="margin-top:18px"><h2>Auditoría reciente</h2><div class="table">@forelse($logs as $x)<div class="row"><div><strong>{{$x->event}}</strong><small>{{$x->description}}</small></div><span>{{$x->ip}}</span><small>{{$x->created_at}}</small></div>@empty<p class="muted">Sin eventos.</p>@endforelse</div></section>
@endsection
