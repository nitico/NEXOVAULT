# NexoVault — Checklist previo a producción

Esta versión prepara el cierre funcional, pero **no debe publicarse con credenciales reales hasta completar este checklist en el VPS**.

## Obligatorio antes de introducir secretos reales
1. VPS actualizado y dedicado o correctamente aislado.
2. HTTPS válido y redirección HTTP → HTTPS.
3. `APP_ENV=production`, `APP_DEBUG=false` y `APP_URL` HTTPS definitivo.
4. Generar `APP_KEY` en el servidor; nunca reutilizar una clave publicada o de ejemplo.
5. Usuario MySQL exclusivo de NexoVault con privilegios mínimos sobre una única BD.
6. `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true`, `SESSION_SAME_SITE=strict`.
7. Extensión PHP Sodium activa.
8. Directorio del proyecto sin acceso web salvo `public/`; `.env`, backups y storage nunca públicos.
9. Microsoft Authenticator activado y códigos de recuperación guardados fuera del VPS.
10. Sustituir la contraseña maestra de desarrollo `12345678` por la contraseña definitiva elegida exclusivamente por el propietario.
11. Ejecutar una prueba real de backup cifrado y restauración usando datos ficticios.
12. Configurar backups cifrados fuera del VPS y política de retención.
13. Revisar logs/permisos y confirmar que no contienen secretos.
14. `php artisan optimize`, migraciones y build de producción completados sin errores.
15. QA final: bloqueo automático, login + MFA, recovery code, cambio de clave maestra, crear/revelar/editar credencial, backup/restauración y logout/bloqueo.

## Seguridad incluida en esta versión
- Cifrado de secretos mediante la bóveda existente y Sodium.
- MFA TOTP compatible con Microsoft Authenticator.
- Setup MFA temporal con expiración de 10 minutos.
- Reautenticación con contraseña maestra + TOTP para desactivar MFA, regenerar códigos y cambiar la contraseña maestra.
- Códigos de recuperación de un solo uso para acceso.
- Throttling en acciones críticas.
- Bloqueo automático por inactividad.
- Auditoría de eventos sensibles.
- Encabezados HTTP defensivos (CSP, frame denial, no-sniff, no-referrer, permissions policy y HSTS cuando HTTPS está activo).
- Respuestas de la aplicación marcadas no-cache/no-store.
- Confirmación antes de reemplazar accidentalmente una contraseña ya escrita con el generador.

## Pendiente deliberado para despliegue
La URL/dominio real, certificado TLS, firewall, servicio web (Apache/Nginx), usuario del sistema, credenciales de BD y estrategia externa de backups dependen del VPS definitivo y deben configurarse allí, no hardcodearse en el proyecto.
