# NexoVault V0.2 — Correcciones y mejoras

- Corregida la vista `auth/vault.blade.php` que provocaba el `unexpected else`.
- Corregido el instalador para crear `storage/framework/views`, `cache`, `sessions` y `storage/logs`.
- Puerto local estándar de NexoVault: `8787`.
- Contraseña maestra temporal de desarrollo: `12345678` (solo entorno `local`).
- En producción la validación vuelve a exigir mínimo 12 caracteres; la contraseña de desarrollo no es una puerta trasera ni un bypass.
- Nueva pantalla visual de configuración/desbloqueo de bóveda.
- Layout principal reorganizado y legible.
- Recompilación de assets requerida para aplicar el CSS propio de NexoVault.
- Se mantiene Argon2id/libsodium y el esquema DEK/KEK existente.
