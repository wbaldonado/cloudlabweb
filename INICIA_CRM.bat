@echo off
chcp 65001 >nul
title Iniciar Sistema CRM
color 0B
cls
echo ================================================
echo     INICIAR SISTEMA CRM
echo ================================================
echo.
echo Este script iniciará AMBOS servidores necesarios:
echo   1. API Backend CRM (Puerto 8001)
echo   2. Frontend CRM (Puerto 3000)
echo.
echo ================================================
echo.
echo Presione cualquier tecla para continuar...
pause >nul
cls

REM Verificar PHP
echo Verificando PHP...
php --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: PHP no encontrado!
    echo Por favor, instale XAMPP o agregue PHP al PATH
    pause
    exit /b 1
)
echo ✓ PHP encontrado
echo.

REM Verificar Node.js
echo Verificando Node.js...
node --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Node.js no encontrado!
    echo Por favor, instale Node.js desde https://nodejs.org/
    pause
    exit /b 1
)
echo ✓ Node.js encontrado
echo.

REM Verificar si node_modules existe
echo Verificando dependencias del frontend...
cd /d C:\xampp\htdocs\crm\frontend
if not exist "node_modules" (
    echo.
    echo Instalando dependencias del frontend...
    echo Esto puede tomar varios minutos la primera vez...
    echo.
    call npm install
    if errorlevel 1 (
        echo.
        echo ERROR al instalar dependencias!
        pause
        exit /b 1
    )
    echo.
    echo ✓ Dependencias instaladas
    echo.
) else (
    echo ✓ Dependencias ya instaladas
)
echo.

REM Volver al directorio base
cd /d C:\xampp\htdocs\cloudlablatam1

REM Iniciar API Backend
echo ================================================
echo [1/2] Iniciando API Backend (Puerto 8001)...
echo ================================================
start "CRM API Backend - Puerto 8001" cmd /k "chcp 65001 >nul ^& title CRM API Backend ^& color 0A ^& cd /d C:\xampp\htdocs\crm ^& echo ======================================== ^& echo   CRM API Backend ^& echo ======================================== ^& echo. ^& echo Iniciando servidor en puerto 8001... ^& echo. ^& php artisan serve --port=8001"

REM Esperar un momento
timeout /t 3 /nobreak >nul

REM Iniciar Frontend
echo ================================================
echo [2/2] Iniciando Frontend (Puerto 3000)...
echo ================================================
start "CRM Frontend - Puerto 3000" cmd /k "chcp 65001 >nul ^& title CRM Frontend ^& color 0E ^& cd /d C:\xampp\htdocs\crm\frontend ^& echo ======================================== ^& echo   CRM Frontend ^& echo ======================================== ^& echo. ^& echo Iniciando servidor en puerto 3000... ^& echo. ^& npm run dev"

cls
echo ================================================
echo     SERVICIOS INICIADOS
echo ================================================
echo.
echo Se han abierto DOS ventanas nuevas:
echo   - CRM API Backend (Ventana verde)
echo   - CRM Frontend (Ventana amarilla)
echo.
echo ESPERE unos segundos para que ambos servidores
echo inicien completamente.
echo.
echo Cuando vea en las ventanas:
echo   API: "Starting Laravel development server..."
echo   Frontend: "Local: http://localhost:3000/"
echo.
echo Entonces:
echo   1. Vaya al panel de administración
echo   2. Haga clic en "CRM System" en el menú lateral
echo   3. El CRM se abrirá en http://localhost:3000
echo.
echo IMPORTANTE: MANTENGA AMBAS VENTANAS ABIERTAS
echo mientras use el CRM!
echo.
echo ================================================
echo.
pause

