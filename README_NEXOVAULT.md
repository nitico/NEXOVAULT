# NexoVault V0.1
Centro privado para proyectos, credenciales cifradas, URLs/recursos y planificación.

## Instalación local (Windows/XAMPP)
1. Copiar como `C:\xampp\htdocs\NEXOVAULT`.
2. Crear una BD MySQL vacía llamada `nexovault`.
3. Copiar `.env.example` a `.env` y ajustar DB si hace falta.
4. Ejecutar:
   - `composer install`
   - `php artisan key:generate`
   - `php artisan migrate`
   - `npm.cmd install` (solo si node_modules no existe)
   - `npm.cmd run build`
   - `php artisan serve`
5. Abrir `http://127.0.0.1:8000`.
6. En el primer acceso crear una contraseña maestra DE DESARROLLO de al menos 12 caracteres. No usar la contraseña final de producción todavía.

## Seguridad implementada
- La contraseña maestra no se guarda.
- Argon2id (libsodium) deriva una KEK desde contraseña + salt aleatorio.
- Un DEK aleatorio cifra las credenciales mediante XSalsa20-Poly1305/secretbox autenticado.
- El DEK queda envuelto con la KEK y solo se puede abrir con la contraseña maestra.
- La clave desbloqueada en sesión se protege adicionalmente con Laravel Crypt/APP_KEY.
- Rate limit en desbloqueo y setup.
- CSRF de Laravel.

## Antes de producción
Pendiente: cambio seguro de contraseña maestra, bloqueo automático reforzado, auditoría, backups cifrados, 2FA opcional, política CSP/cabeceras, HTTPS, hardening VPS, exportación/recuperación y revisión criptográfica independiente.
