# NexoBarber — avance Portal Barbería / Agenda

## Implementado
- Agenda interna por barbería con aislamiento por `peluqueria_id`.
- Acceso de BARBERO a su propio dashboard, agenda y disponibilidad.
- Horario semanal por barbero y bloqueos extraordinarios.
- Validación de solapamiento de citas, bloqueos y disponibilidad antes de guardar.
- Citas con múltiples servicios y snapshot de nombre, precio y duración.
- Cancelación con motivo, actor y auditoría; el histórico no se elimina.
- Configuración de servicio/precio especial por barbero mediante `servicio_barbero`.
- Dashboard con citas del día.
- Navegación por rol: barberos no reciben controles de equipo, catálogo ni pagos.
- Responsive adicional para agenda, disponibilidad y configuración por barbero.

## Base de datos
Migración nueva: `2026_09_24_040000_create_agenda_tables.php`.
Tablas: `servicio_barbero`, `disponibilidades_barbero`, `bloqueos_agenda`, `citas`, `cita_servicio`.

## Ejecutar en Windows
1. `php artisan migrate`
2. `php artisan optimize:clear`
3. `npm.cmd run build`
4. `php artisan test`

## Validación en este entorno
- Sintaxis PHP de controladores, modelos, migración y rutas: OK.
- `php artisan route:list --path=mi-barberia`: 19 rutas detectadas.
- La suite PHPUnit no puede ejecutarse aquí porque este PHP carece de DOM, mbstring y xmlwriter.
- Vite no puede compilar aquí porque `node_modules` proviene de Windows y no contiene el binario opcional Linux de Rollup. Debe compilarse en el entorno Windows del proyecto.

## Recurso visual protegido
`public/images/nexobarber/auth-barbershop.png` NO fue modificado. SHA-256 verificado: `48046874b0cff0bfe5cc595082bfce1734f6b72ca3d5690c02df12cc35d1f416`.

## Siguiente bloque natural
Reserva pública/cliente, cálculo de slots disponibles, política de cancelación/no-show, y posteriormente notificaciones.
