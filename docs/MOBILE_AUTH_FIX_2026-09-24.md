# Corrección de autenticación móvil LAN — 2026-09-24

## Causa compatible con el síntoma observado
Durante QA por LAN, `.env` usa `APP_URL=http://localhost`. Los formularios y enlaces de autenticación utilizaban `route()` con URL absoluta. En una PC, `localhost` apunta a la propia PC y el login puede funcionar; en un teléfono, `localhost` apunta al teléfono. El síntoma observado fue que el móvil cargaba `/login` desde la IP de la PC, pero al enviar no aparecía ningún `POST /login` en el servidor.

## Corrección
Todas las acciones y enlaces de las vistas de autenticación se generan ahora como rutas relativas (`route(..., [], false)`). Así el navegador conserva el host real con el que abrió NexoBarber, sea `127.0.0.1`, una IP LAN o el dominio de producción.

No se modificaron credenciales, sesiones, controladores de autenticación, base de datos ni la imagen institucional de autenticación.

## Prueba LAN
1. PC y teléfono en la misma red.
2. Ejecutar `php artisan serve --host=0.0.0.0 --port=8000`.
3. Abrir desde el móvil `http://IP-DE-LA-PC:8000/login`.
4. Iniciar sesión y confirmar en la terminal que aparece `POST /login`, seguido de `/dashboard` y la ruta correspondiente al rol.

Para PWA instalable y producción se utilizará HTTPS; los service workers no deben depender de una IP LAN HTTP.
