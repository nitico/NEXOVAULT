# NexoVault V0.3 — ciclo funcional y seguridad

- Proyectos ampliados: prioridad, ruta local, repositorio, fecha de inicio, archivado y progreso automático.
- Hitos ponderados recalculan el progreso del proyecto cuando el modo automático está activo.
- Dashboard ampliado con pendientes y actividad reciente.
- Auditoría persistente para eventos críticos.
- Bloqueo automático por inactividad (15 min por defecto, configurable con NEXOVAULT_IDLE_TIMEOUT).
- Microsoft Authenticator mediante TOTP RFC 6238, con códigos de recuperación de un solo uso.
- Flujo MFA: contraseña maestra -> segundo factor -> desbloqueo.
- Cambio seguro de contraseña maestra reenvolviendo la DEK, sin recifrar todas las credenciales.
- Exportación de backup cifrado `.nvault`.
- Generador criptográfico de contraseñas en navegador.
- Validación explícita de Sodium en configuración inicial.

## Antes de producción
Quedan para el ciclo de endurecimiento/QA: restauración asistida de backup, HTTPS/VPS, cookies secure, cabeceras CSP/HSTS, política final de contraseña, pruebas de recuperación, QR visual para TOTP y pruebas integrales de autorización/seguridad.
