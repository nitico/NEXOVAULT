# Avance NexoBarber — reservas de clientes

Se añadió sobre la línea base aprobada, sin limpiar datos ni modificar migraciones históricas:

- Reserva pública por barbería, compatible con clientes autenticados e invitados según política.
- Selección múltiple de servicios, barbero específico o cualquiera disponible.
- Cálculo de slots a partir de disponibilidad semanal, bloqueos, citas existentes y duración total.
- Revalidación del slot al guardar para evitar reservas sobre horarios ya ocupados.
- Snapshot de servicios, precios y duración dentro de cada cita.
- Código y token de gestión de cita.
- Confirmación pública y cancelación mediante token con ventana configurable.
- Portal `Mis citas` para clientes autenticados.
- Políticas por barbería: invitados, anticipación, horizonte de reserva, cancelación y no-show.
- Estados de asistencia COMPLETADA / NO_SHOW desde la agenda interna.
- Notificación interna persistente a propietarios/administradores cuando entra una nueva cita (base para centro de notificaciones posterior).
- Auditoría de reservas y cambios administrativos relevantes.
- Migración incremental `2026_09_24_050000_create_customer_booking_tables.php`.

## Validación local del entorno de trabajo

- Sintaxis PHP revisada en `app/` y migraciones: sin errores.
- Rutas públicas de reserva: 5 registradas.
- Rutas `mi-barberia`: 22 registradas.
- Blade compilado correctamente mediante `php artisan view:cache`.
- La imagen aprobada `public/images/nexobarber/auth-barbershop.png` se preservó; SHA-256 durante esta entrega: `48046874b0cff0bfe5cc595082bfce1734f6b72ca3d5690c02df12cc35d1f416`.
- No fue posible ejecutar migración SQLite en este runtime porque PHP no dispone del driver PDO SQLite. Ejecutar migraciones y suite en el XAMPP del proyecto.

## Ejecutar en la PC del proyecto

```powershell
php artisan migrate
php artisan optimize:clear
npm.cmd run build
php artisan test
```
