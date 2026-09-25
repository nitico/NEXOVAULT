# Informe de facturación y cuenta corriente

Entrega sobre la versión actual de NEXOBARBER. Se conservaron los registros reales y de QA existentes. No se ejecutaron limpiezas, `migrate:fresh`, reinicios de base de datos ni modificaciones de migraciones históricas.

## 1. Causas encontradas

La generación de períodos estaba vinculada a visitas GET; la aprobación de pagos podía activar la suscripción; faltaban bloqueos y restricciones para garantizar una sola aplicación de un pago. Los cálculos usaban flotantes y la interfaz no separaba claramente cobros de otros movimientos. El formulario utilizaba clases sin definición y el estilo genérico de inputs ocultaba la marca del checkbox de efectivo.

El selector vacío tenía una causa adicional: los dos períodos existentes tenían total cero y estaban saldados. Se conservaron esos snapshots; la interfaz ahora explica la situación y ofrece acceso a configuración y generación explícita.

## 2. Archivos implementados

Nuevos:

- `app/Support/Dinero.php`
- `app/Services/PagoFacturacionService.php`
- `app/Services/OperacionBarberiaService.php`
- `app/Http/Requests/RegistrarMovimientoRequest.php`
- `app/Http/Controllers/SuperAdmin/PeriodoFacturacionController.php`
- `app/Console/Commands/GenerarPeriodosFacturacion.php`
- `database/migrations/2026_09_24_020000_strengthen_financial_integrity.php`
- `resources/js/facturacion.js`
- `resources/views/components/admin-errors.blade.php`
- `resources/views/superadmin/periodos/create.blade.php`
- `resources/views/superadmin/periodos/show.blade.php`
- `resources/views/superadmin/peluquerias/cuenta-corriente.blade.php`
- `resources/views/superadmin/peluquerias/control-operativo.blade.php`
- `tests/Feature/FacturacionTest.php`

Modificados:

- `app/Services/FacturacionService.php`
- `app/Models/PeriodoFacturacion.php`, `MovimientoFacturacion.php`, `Pago.php`
- `app/Http/Controllers/SuperAdmin/PagoAdminController.php`, `PeluqueriaAdminController.php`, `ConfiguracionAdminController.php`
- `app/Http/Controllers/PagoController.php`
- `routes/web.php`, `routes/console.php`
- `resources/js/app.js`, `resources/css/app.css`
- `resources/views/superadmin/pagos/create.blade.php`, `index.blade.php`, `show.blade.php`
- `resources/views/superadmin/peluquerias/show.blade.php`
- `resources/views/superadmin/configuracion/edit.blade.php`
- `resources/views/pagos/create.blade.php`
- `phpunit.xml`, `tests/TestCase.php`
- Manifest y assets compilados de `public/build`.

La vista y el controlador del reporte existente del propietario recibieron únicamente ajustes de integración y validación; no se desarrolló un nuevo portal. Los respaldos previos y la comprobación de integridad están en `storage/app/finance-audit`.

## 3. Migración nueva

`2026_09_24_020000_strengthen_financial_integrity.php`, aplicada correctamente. Las 14 migraciones figuran ejecutadas. La migración comprueba duplicados antes de añadir la restricción y falla sin intentar corregir ni eliminar datos históricos.

## 4. Tablas, columnas e índices

- `periodos_facturacion`: `fecha_vencimiento`, `dias_gracia`, `dias_pago_inicial`, índice de vencimiento. Los períodos anteriores no se recalculan ni rellenan retroactivamente.
- `movimientos_facturacion`: `origen`, `clave_idempotencia` UUID única e índice único de `pago_id`.
- `pagos`: `origen`; compatibilidad del método `EFECTIVO` junto a `TRANSFERENCIA` también en instalaciones SQLite nuevas.
- Relaciones financieras de períodos, detalles, movimientos y pagos protegidas con claves foráneas RESTRICT para impedir borrados en cascada del historial.
- Se conservan los índices existentes de barbería/mes y número de recibo únicos.

## 5. Flujo final de facturación

La generación explícita toma un snapshot de tarifas, integrantes activos, incluidos, extras y vencimiento. Los propietarios y clientes no generan cargos. Repetir barbería/mes devuelve el período existente sin rehacer sus detalles. Los cambios de configuración o equipo afectan generaciones posteriores, no snapshots anteriores.

El saldo se deriva del total facturado menos el libro de movimientos; no existe un segundo saldo editable. La aritmética usa centavos enteros. Las escrituras se ejecutan en transacciones, bloqueando el período y revalidando su saldo antes de aplicar el movimiento. Los movimientos y snapshots quedan protegidos contra edición ordinaria.

## 6. Transferencias

SUPERADMIN puede registrar transferencias parciales, con comprobante opcional privado y origen SUPERADMIN. El reporte existente de la barbería mantiene comprobante obligatorio, origen BARBERIA y aprobación/rechazo. Se valida pertenencia del período. Aprobar bloquea y aplica una sola vez; rechazar no reduce saldo. Un pago histórico sin período requiere asociación explícita al aprobarlo.

## 7. Efectivo

Disponible según configuración. Cada abono genera pago aprobado, movimiento y recibo único `REC-año-ID` dentro de la misma transacción. Se puede deshabilitar efectivo sin bloquear transferencias.

## 8. Intercambio

Exige descripción y reduce saldo. Se registra separadamente y no incrementa efectivo ni dinero cobrado.

## 9. Condonación

Exige motivo, conserva el importe originalmente facturado y reduce saldo mediante un movimiento auditable. No se contabiliza como ingreso.

## 10. Abonos parciales

Se admiten múltiples cuotas y combinaciones de los cuatro tipos hasta saldo cero. Se rechazan importes no positivos, más de dos decimales y montos superiores al saldo vigente. El UUID evita duplicaciones por reenvío del formulario; `pago_id` único evita aplicar dos veces el mismo pago. El historial muestra fecha, tipo, importe, origen, descripción, actor y recibo cuando corresponde.

## 11. Operación y deuda

El estado operativo es independiente de PENDIENTE, PARCIAL, SALDADO o VENCIDO. Un período parcialmente pagado cuyo vencimiento pasó se presenta como VENCIDO. Pagar no reactiva ni extiende automáticamente una suscripción; deber no suspende automáticamente. Suspender exige motivo y registra actor/fecha; reactivar con deuda está permitido y auditado.

## 12. Generación automática

Comando: `php artisan facturacion:generar`. Opciones: `--peluqueria=ID`, `--periodo=AAAA-MM` y `--dry-run`. La tarea Laravel está registrada diariamente a las 00:15 de America/Santo_Domingo, sin solapamientos.

Para una barbería sin historial comienza en el mes actual; con historial recupera meses faltantes desde el primero existente hasta el actual. No inventa deuda previa al historial. Los meses faltantes se generan con las tarifas y equipo disponibles al momento de generarlos. Se excluyen meses futuros o anteriores a la aprobación. La generación manual ofrece el mismo comportamiento idempotente. Ningún GET genera deuda.

El servidor debe ejecutar `php artisan schedule:run` cada minuto desde la raíz del proyecto, usando cron o el Programador de tareas de Windows. Se registró el calendario Laravel; no se instaló una tarea del sistema operativo en esta intervención.

## 13. Validación

- `php artisan migrate`: correcto; migración incremental aplicada.
- `php artisan migrate:status`: 14 migraciones ejecutadas.
- `php artisan optimize:clear`: correcto.
- `php artisan route:list`: 56 rutas; nuevas rutas protegidas por autorización de SUPERADMIN.
- `php artisan test`: **66 pruebas aprobadas, 205 aserciones**, incluidas 41 pruebas financieras. Sin fallos pendientes.
- Automatización con SQLite en memoria, sin usar la base real incluso ante configuración cacheada.
- Cobertura de los casos A–R solicitados: generación, duplicados, integrantes incluidos y adicionales, efectivo y segunda cuota, transferencia, intercambio, condonación, combinación hasta cero, sobrepago, operación con deuda, suspensión/reactivación, aprobación única y conservación histórica. Además: autorización, archivos privados, rechazos, rollback, idempotencia, vencimiento, precisión decimal, restricciones de base de datos y GET sin escrituras.
- Blade compilado y 180 archivos PHP/Blade revisados: cero errores de sintaxis.
- Comparación de huellas antes/después: registros y columnas preexistentes sin cambios en 11 tablas, incluidas barberías, usuarios, pagos, períodos, detalles, movimientos, configuración y auditorías.
- Revisión visual autenticada de registro, pagos, configuración, generación, cuenta corriente y detalle mensual. Matriz orientada a 1920×1080, 1366×768, 1024×768 y 768×1024; hubo redondeos de un píxel por zoom del navegador. Sin desbordamiento horizontal ni superposición de controles.
- Formulario con saldo e historial de varios abonos verificados con vistas temporales de modelos no persistidos: campos dinámicos, descripción requerida y rechazo de importe superior al saldo. Estas vistas temporales se retiraron; no se borraron datos de QA de la base.
- Las pruebas verifican restricciones y solicitudes secuenciales con saldo actualizado; no equivalen a una prueba de carga concurrente de MySQL.

## 14. Vite

`npm.cmd run build`: correcto, 60 módulos. Assets finales: `app-DqzjseUA.css` y `app-BHdArGxG.js`. Verificado en navegador que carga el CSS final y que el checkbox de efectivo muestra su marca. Vite conserva un aviso no fatal sobre la resolución en runtime de la imagen de autenticación existente.

## 15. Pendientes reales

1. Configurar las tarifas comerciales definitivas: actualmente la base mensual conserva su valor cero. No se asignaron precios arbitrarios. Los dos períodos históricos de importe cero permanecen saldados y no se revalorizan.
2. Activar el ejecutor del scheduler en el servidor para que el calendario registrado se ejecute sin intervención manual.
3. La limpieza de datos de QA para producción queda expresamente fuera de esta tarea, tal como se solicitó.

El bloque financiero está implementado y validado. No se desarrollaron módulos nuevos fuera de este alcance.
