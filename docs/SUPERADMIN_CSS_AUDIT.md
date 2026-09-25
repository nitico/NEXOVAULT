# Auditoría visual SUPERADMIN — 2026-09-23

## Causa raíz

No era un problema de caché. El layout `layouts/superadmin.blade.php` carga app.css y app.js mediante @vite, y los siete módulos heredan este layout. No se encontraron bloques <style> ni atributos style en estas vistas administrativas.

- `.nb-panel-head` tenía padding, flex y borde; `.nb-panel-header`, usado en Configuración y fichas de Pagos, Usuarios y Barberías, carecía de ese contrato estructural. El refinamiento posterior solo daba tipografía a sus descendientes. El overflow:hidden de Configuración hacía especialmente visible la proximidad al borde.
- Pagos usaba `.nb-eyebrow`, sin definición, en vez del componente existente `.nb-admin-kicker`.
- CSS agregado por versiones repetía columnas de Barberías y el color del badge pendiente. Las media queries estaban intercaladas con reglas de versiones posteriores.
- En anchos intermedios, los mínimos de las columnas superaban el espacio disponible después de restar sidebar y padding. Algunos encabezados seguían en escritorio mientras las filas cambiaban.
- `.nb-barber-row .nb-status:first-of-type` no seleccionaba el primer badge: el avatar ya era el primer span. La flecha seguía ocupando una celda adicional en algunas filas compactas.
- `.nb-admin-heading.compact` tenía mayor especificidad que la alineación responsive, centrando el encabezado cuando la dirección pasaba a columna.

## Cambios controlados

Archivos de aplicación modificados:

1. `resources/css/app.css`.
2. `resources/views/superadmin/pagos/index.blade.php`: solamente la clase de FINANZAS.
3. Assets regenerados en `public/build`, incluido `manifest.json`.

Reglas:

- Base compartida `.nb-panel-head, .nb-panel-header`: flex, gap de 18px, padding de 21px 23px y borde existente. Se conservan las variantes tipográficas anteriores.
- Tipografía de `.nb-panel-header` expresada sin !important: eyebrow en bloque, margen inferior de 5px y line-height 1.5; título de 16px, peso 600, margen cero y line-height 1.5.
- Grids de Configuración con `minmax(0,1fr)`; input con sufijo flexible y min-width:0. El foco se mantiene en el contenedor sin !important.
- Eliminadas definiciones anteriores ya sobrescritas de columnas de Barberías y duplicación del badge pendiente, manteniendo el color efectivo que ya se mostraba.
- Listas densas de Solicitudes, Barberías y Pagos pasan al formato compacto a 1200px. Usuarios y Auditoría a 1100px. Encabezados, filas y celdas ocultas se coordinan; las flechas redundantes no crean filas implícitas. Los enlaces de las filas se conservan.
- Encabezados compactos alineados al inicio en la media query existente de 850px.

No se modificaron controladores, modelos, rutas, migraciones ni lógica de negocio. No se guardaron formularios administrativos ni se crearon registros para la validación.

## Validación

- `npm.cmd run build`: correcto, Vite 6.4.3, 59 módulos, 1.53 segundos en la ejecución final. Se utilizó npm.cmd porque PowerShell bloquea npm.ps1. La compilación necesitó acceso fuera del sandbox para el temporal de Vite dentro de node_modules.
- CSS final: `public/build/assets/app-CUBHrpz2.css`; JS: `app-D99hXOC0.js`.
- `php artisan optimize:clear`: correcto.
- `php artisan view:cache`: correcto. Se verificaron con php -l 104 archivos Blade compilados: cero errores. Después se ejecutó `php artisan view:clear` correctamente.
- `php artisan route:list --path=superadmin`: 23 rutas disponibles.
- Suite existente ejecutada con DB_CONNECTION=sqlite, DB_DATABASE=:memory:, DB_URL vacío y caché/sesión array, sin usar la base de datos del proyecto: 24 pruebas aprobadas, 1 fallida, 59 aserciones.
- Fallo ajeno a CSS: `RegistrationTest::test_new_users_can_register` no envía telefono, campo requerido por RegisteredUserController. No se alteró esa lógica ni la prueba para ocultar el fallo.

Revisión en navegador autenticado: Dashboard, Barberías, Solicitudes, Pagos, Usuarios, Configuración y Auditoría. Se verificaron las URL de sus hojas compiladas, display/grid, dimensiones y desbordamientos a 1920x1080, 1366x768, aproximadamente 1024x768 y 768x1024. El escalado del navegador redondeó 1024 a 1025px en algunas mediciones; se inspeccionaron los anchos CSS efectivos, no solo los solicitados. Sin desbordamientos detectados en el contenido revisado.

Configuración conserva su encabezado de página y presenta ambos eyebrows a unos 22px del borde de sus tarjetas. Dos columnas en escritorio y una en tablet. Se inspeccionaron capturas de Configuración, Pagos, Usuarios y Auditoría en laptop, y de Configuración, Barberías, Usuarios y Auditoría en tablet.

Se revisaron además fichas reales de Solicitudes, Usuarios y Barberías. Solicitudes se comprobó con el filtro Todas para incluir una fila existente. Pagos no tenía registros: se verificó su estado vacío real y se renderizaron sus vistas Blade de listado y ficha con modelos ficticios solo en memoria, sin persistencia. Ambas vistas temporales se comprobaron a 1920, 1366 y 768px y se retiraron del directorio público al terminar.

## Pendientes y límites

- Actualizar por separado la prueba de registro para ajustarla a los requisitos vigentes (telefono y preparación del rol CLIENTE).
- Vite avisa que `/images/nexobarber/auth-barbershop.png` se resolverá en runtime. El archivo existe en public/images/nexobarber; es una referencia del área de autenticación, no del portal SUPERADMIN. No se cambió en esta tarea.
- No se ejecutaron acciones de aprobar/rechazar pagos, guardar configuración o cambiar estados: esta tarea validó presentación, sin modificar datos de negocio.
- El directorio no contiene repositorio Git. Se conservaron copias previas de los dos archivos fuente en `storage/app/css-audit/` y el inventario de rutas en ese mismo directorio.
