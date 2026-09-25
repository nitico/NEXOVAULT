@echo off
setlocal
cd /d C:\xampp\htdocs\NEXOVAULT
if not exist storage\framework\views mkdir storage\framework\views
if not exist storage\framework\cache mkdir storage\framework\cache
if not exist storage\framework\sessions mkdir storage\framework\sessions
if not exist storage\logs mkdir storage\logs
if not exist .env copy .env.example .env
call composer install
php artisan key:generate --force
php artisan optimize:clear
php artisan migrate
call npm.cmd install
call npm.cmd run build
php artisan optimize:clear
echo.
echo NexoVault preparado.
echo URL de desarrollo: http://127.0.0.1:8787
echo Inicia con: php artisan serve --host=127.0.0.1 --port=8787
pause
