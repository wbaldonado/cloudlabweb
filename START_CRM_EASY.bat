@echo off
title CRM System Starter
color 0B
cls
echo ================================================
echo     CRM System Starter
echo ================================================
echo.
echo This will start BOTH servers needed for CRM:
echo   1. CRM API Backend (Port 8001)
echo   2. CRM Frontend (Port 3000)
echo.
echo ================================================
echo.
echo Starting servers...
echo.

REM Start API Backend
echo [1/2] Starting CRM API Backend on port 8001...
start "CRM API Backend - Port 8001" cmd /k "title CRM API Backend ^& color 0A ^& cd /d C:\xampp\htdocs\crm ^& echo Starting CRM API Backend... ^& echo. ^& php artisan serve --port=8001"

REM Wait a moment
timeout /t 2 /nobreak >nul

REM Start Frontend
echo [2/2] Starting CRM Frontend on port 3000...
start "CRM Frontend - Port 3000" cmd /k "title CRM Frontend ^& color 0E ^& cd /d C:\xampp\htdocs\crm\frontend ^& echo Starting CRM Frontend... ^& echo. ^& if not exist node_modules (echo Installing dependencies... ^& call npm install ^& echo.) ^& npm run dev"

echo.
echo ================================================
echo     Servers Starting!
echo ================================================
echo.
echo Two new windows have opened:
echo   - CRM API Backend (Green window)
echo   - CRM Frontend (Yellow window)
echo.
echo Wait for both to start, then:
echo   1. Login to admin panel
echo   2. Click "CRM System" in sidebar
echo   3. CRM will open at http://localhost:3000
echo.
echo KEEP BOTH WINDOWS OPEN while using CRM!
echo.
echo ================================================
pause

