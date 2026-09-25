# NexoBarber — cierre funcional previo a QA (24/09/2026)

Este corte parte de `NEXOBARBER_NUEVO.zip` recibido después de la auditoría de Codex.

## Incorporado
- Redirección correcta del rol CLIENTE hacia su portal, en lugar del selector de barberías.
- Portal cliente con resumen de próximas citas, completadas y no-show.
- Historial de citas y reprogramación autenticada con comprobación de disponibilidad y aislamiento por cliente.
- Configuración operativa de barbería para propietario/administrador: nombre comercial, contacto, dirección y zona horaria. Los datos legales sensibles permanecen bajo SUPERADMIN.
- Enlace de Configuración integrado al portal de barbería.
- Base PWA: manifest, service worker de shell/assets y metadatos de instalación. El SW se registra solo en HTTPS/localhost para no interferir con QA por HTTP en LAN.
- Metadatos PWA en SUPERADMIN, portal de barbería, cliente y autenticación.
- Mensajes base de autenticación/validación en español y locale español por defecto.
- Navegación de cliente con acceso a Mis citas.
- Corrección del casting inconsistente del modelo Cita.
- `AgendaDisponibilidadService::disponible()` admite ignorar una cita concreta para reprogramarla sin colisionar consigo misma.
- Ajustes responsive adicionales para portal cliente y portal barbería.

## No alterado
- No se ejecutó limpieza de BD ni se eliminaron datos de prueba.
- No se modificaron migraciones históricas ni datos de negocio existentes.
- No se modificó `public/images/nexobarber/auth-barbershop.png`.

## Integridad de imagen protegida
SHA-256 comprobado: `48046874b0cff0bfe5cc595082bfce1734f6b72ca3d5690c02df12cc35d1f416`.

## Validaciones realizadas en este entorno
- Sintaxis PHP de todo `app/`: correcta.
- Laravel reconoce 90 rutas.
- Hash de imagen protegida: correcto.
- PHPUnit no pudo ejecutarse aquí porque PHP carece de DOM/mbstring/xmlwriter.
- `view:cache` tampoco puede finalizar por ausencia de DOMDocument en Termwind.
- Vite no puede compilar aquí porque `node_modules` proviene de Windows y el ejecutable local no es utilizable en Linux. Debe validarse con `npm.cmd run build` en XAMPP/Windows.

## QA recomendado
Realizar el QA integral sobre esta versión y consolidar defectos antes de iniciar la app nativa. No limpiar la BD hasta cerrar ese QA.
