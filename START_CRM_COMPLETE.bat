@echo off
echo Starting Complete CRM System...
echo.
echo This will start:
echo 1. CRM API Backend (port 8001)
echo 2. CRM Frontend (port 3000)
echo.
echo Press any key to start...
pause >nul

start "CRM API Backend" cmd /k "cd C:\xampp\htdocs\crm && php artisan serve --port=8001"
timeout /t 3 /nobreak >nul
start "CRM Frontend" cmd /k "cd C:\xampp\htdocs\crm\frontend && npm run dev"

echo.
echo CRM System started!
echo API: http://localhost:8001
echo Frontend: http://localhost:3000
echo.
echo Close the windows to stop the services.
pause

