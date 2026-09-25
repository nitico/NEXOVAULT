# NexoVault V0.4 — ciclo funcional consolidado

## Incorporado
- Búsqueda global de proyectos, credenciales, URLs/recursos y planificación.
- Ficha de proyecto consolidada con accesos directos a hitos, credenciales y recursos.
- Historial técnico de avance de proyecto (automático/manual).
- Metadatos de credenciales: fecha de cambio de clave y último revelado.
- Favoritos de credenciales.
- Eliminación funcional de credenciales, recursos y elementos de planificación con auditoría.
- Auditoría añadida a URLs/recursos.
- Regeneración de códigos de recuperación MFA validando contraseña maestra.
- Centro de Backup y Restauración.
- Restauración de `.nvault` con validación de cabecera, descifrado, estructura y transacción SQL.
- Navegación actualizada y mejoras visuales/formularios.

## Seguridad / producción pendiente
- No usar credenciales reales todavía.
- Contraseña 12345678 solo para desarrollo local.
- Antes del VPS: HTTPS, cookies seguras, CSP/HSTS, APP_DEBUG=false, rotación de contraseña maestra, MFA obligatorio, pruebas de backup/restauración y hardening del servidor.
- QR visual para alta de Authenticator sigue pendiente; el alta manual TOTP es funcional y evita depender de servicios QR externos.
