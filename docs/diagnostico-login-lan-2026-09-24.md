# Diagnóstico de login LAN — 24/09/2026

## Estado

Incidencia móvil original NO reproducida ni declarada resuelta. No se identificó una causa raíz demostrable del bloqueo en el teléfono. No se modificó código de autenticación, CSS, sesiones, seguridad ni datos de usuarios para ocultar el síntoma.

## Evidencia

- Al iniciar no había un servidor escuchando en el puerto 8000; Chrome devolvió ERR_CONNECTION_REFUSED. Se inició `php artisan serve --host=10.0.0.74 --port=8000`. La IP local comprobada es 10.0.0.74. Este estado inicial no explica el incidente previo donde sí cargaba la página.
- Chrome tenía sesiones SUPERADMIN existentes tanto en LAN como en 127.0.0.1. GET /login redirigió a /dashboard y /superadmin. En ese documento los IDs del login no existen. Es una explicación reproducida para IDs null, no prueba de lo sucedido en la comprobación anterior ni del fallo del teléfono.
- Después de logout, el DOM LAN contiene un único formulario `nb-login-form`, método post y action `http://10.0.0.74:8000/login`, con CSRF y botón submit asociado.
- Antes de recompilar, Chrome a 390 × 844 envió el formulario con credenciales ficticias y recibió un error de servidor visible `auth.failed`. Después de recompilar, el envío vacío mostró `validation.required` en ambos campos. No hubo bloqueo del submit en estas pruebas.
- Prueba HTTP independiente con Node fetch, conservando cookies de GET /login y enviando su token: POST /login devuelve 422 y error de validación del email en los tres hosts: 10.0.0.74:8000, 127.0.0.1:8000 y localhost:8000. Sin token devuelve 419 en los tres. Esto demuestra POST real por LAN y CSRF activo; no demuestra login exitoso del teléfono.
- Login SUPERADMIN en 127.0.0.1 mediante el botón del formulario llegó a /superadmin; aparentemente el navegador autocompletó credenciales existentes. No se extrajeron ni imprimieron contraseñas. Recargar conservó la sesión. Logout de las sesiones existentes LAN y local permitió volver al formulario como invitado.
- Login móvil: botón de aproximadamente 309 × 46 px, pointer-events auto y touch-action manipulation; decoraciones con pointer-events none; ancho del documento 390 px. Registro: submit de aproximadamente 294 × 49 px, sin desbordamiento horizontal observado. Esto es viewport responsive de Chrome de escritorio, no emulación táctil ni prueba en dispositivo físico.
- Registro y recuperación cargan en LAN; recuperación conserva validación HTML nativa. No se crearon cuentas reales ni se enviaron correos de recuperación. Sus flujos completos pasan en la suite aislada.

## Archivos y configuración revisados

`resources/views/auth/*`, `resources/views/layouts/guest.blade.php`, componentes Blade de inputs, labels, errores, botones y estado de sesión; `app/View/Components/GuestLayout.php`; `resources/css/app.css`; `resources/js/app.js`, `bootstrap.js`, `facturacion.js`; rutas, controlador de sesión y LoginRequest; configuración de sesión, Vite y bootstrap; manifest y assets de build.

No hay interceptor de submit del login, formularios anidados en guest ni JS de navegación del login. Los interceptores encontrados en otras vistas no se cargan en guest. No se encontró registro de service worker, PWA o manifest web en el proyecto. No existe public/hot ni caché de configuración en bootstrap/cache. No se inspeccionó el almacenamiento del navegador del teléfono.

APP_URL=http://localhost; SESSION_DRIVER=database, SESSION_DOMAIN=null, SESSION_PATH=/, SESSION_ENCRYPT=false. No se cambiaron. Las acciones de autenticación son relativas al host solicitado. GET /login devuelve Cache-Control: no-cache, private. Los assets de CSS y JS cargan con HTTP 200 desde cada host, coincidiendo con el manifest.

No se encontró mojibake real con búsqueda UTF-8 en auth, guest y componentes. Los textos españoles se renderizan correctamente; la lectura inicial de PowerShell sin Encoding UTF8 produjo una representación engañosa.

## Operaciones y archivos generados

- `php artisan test`: 66 pruebas, 205 assertions, todas correctas. Tests/TestCase.php fuerza SQLite :memory:, sesiones/cache/mail aislados. No se ejecutaron migraciones ni seeders sobre la BD de la aplicación.
- `php artisan route:list --except-vendor`: 86 rutas, incluidos GET/POST login, POST logout, registro y recuperación.
- `php artisan view:clear` y `php artisan view:cache`: correctos; se regeneró storage/framework/views.
- `npm.cmd run build`: correcto tras repetir fuera del sandbox por EPERM en node_modules/.vite-temp. Vite 6.4.3, 60 módulos. Advertencia de imagen absoluta conservada para resolución en runtime; imagen no procesada.
- `public/build/manifest.json` regenerado; CSS anterior app-DacFg9FA.css reemplazado por app-BgR8wmvd.css. JS app-BHdArGxG.js conserva su nombre. El cambio de hash CSS por sí solo no demuestra la causa; el submit ya funcionaba antes del build.
- Este informe es el único archivo fuente añadido. No se añadió instrumentación temporal. No hay repositorio Git en esta carpeta para obtener un diff fiable de cambios anteriores.
- Se iniciaron servidores de diagnóstico LAN y loopback en el puerto 8000. Las peticiones normales actualizan sesiones y caché operativa según Laravel; no se editaron cuentas, datos de negocio ni fixtures existentes.

## Imagen protegida

SHA-256 anterior y posterior idénticos:

`48046874b0cff0bfe5cc595082bfce1734f6b72ca3d5690c02df12cc35d1f416`

No se modificó ni regeneró `public/images/nexobarber/auth-barbershop.png`.

## Pendientes reales

1. Reproducir en el teléfono afectado y conocer SO/navegador, URL final y documento de esa pestaña. Comparar el DOM y assets del teléfono con los aquí comprobados; capturar eventos click/submit y red durante el fallo. Sin ello no es responsable atribuirlo a caché, overlays o JS ni prometer una corrección definitiva.
2. Login exitoso LAN por cada rol con cuentas existentes autorizadas; persistencia posterior y logout de esas cuentas. La autenticación programática exitosa está cubierta por PHPUnit, no por credenciales reales de todos los roles en LAN.
3. Se detectó aparte que /dashboard envía todo usuario no SUPERADMIN a barberia.selector, incluso clientes. Requiere revisar el destino deseado del cliente; no se cambió en esta investigación del POST.
4. Los errores muestran claves `auth.failed` y `validation.required` en vez de traducciones. Es una carencia independiente de mensajes, no la causa de ausencia de POST.

No se aplicó un parche especulativo. El próximo paso depende de evidencia del dispositivo afectado.
