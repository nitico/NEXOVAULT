# NexoVault V0.6 — cierre previo a producción

- Endurecimiento del flujo MFA.
- Configuración MFA temporal con expiración y cancelación.
- Desactivar MFA exige contraseña maestra + TOTP + confirmación explícita.
- Regenerar recovery codes exige contraseña maestra + TOTP.
- Cambio de contraseña maestra exige TOTP cuando MFA está activo.
- Recovery codes con formato más legible y corrección de normalización al validarlos.
- Throttling adicional en acciones críticas.
- Middleware de encabezados de seguridad y no-cache.
- Confirmación antes de sobrescribir una contraseña mediante el generador.
- Plantilla `.env.production.example` y checklist de despliegue.

El alta MFA sigue utilizando la clave TOTP manual para no depender de un servicio QR externo. Un QR local puede añadirse después mediante una biblioteca auditada si se desea; no se envía el secreto a terceros.
