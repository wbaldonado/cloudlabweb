@echo off
title CRM Frontend Server
color 0A
echo ========================================
echo    Starting CRM Frontend Server
echo ========================================
echo.
echo This will start the CRM frontend on port 3000
echo Keep this window open while using the CRM!
echo.
echo ========================================
echo.

cd /d C:\xampp\htdocs\crm\frontend

echo Checking if node_modules exists...
if not exist "node_modules" (
    echo.
    echo node_modules not found. Installing dependencies first...
    echo This may take a few minutes...
    echo.
    call npm install
    echo.
    echo Dependencies installed!
    echo.
)

echo.
echo Starting CRM Frontend...
echo.
echo Frontend will be available at: http://localhost:3000
echo.
echo Press Ctrl+C to stop the server
echo.
echo ========================================
echo.

call npm run dev

pause

