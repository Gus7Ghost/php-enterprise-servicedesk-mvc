@echo off
set "PATH=%LOCALAPPDATA%\Programs\php;%PATH%"
cd /d "%~dp0"
echo ============================================================
echo   Iniciando Enterprise Service Desk Portal (PHP 8.3 MVC)
echo ============================================================
echo   URL: http://localhost:8000
echo   Presione Ctrl + C para detener el servidor web.
echo ============================================================
start http://localhost:8000
php -S localhost:8000 -t public
pause
