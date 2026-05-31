@echo off
echo Creating storage symbolic link for Windows...

REM Check if link already exists
if exist "public\storage" (
    echo Removing existing storage link...
    rmdir /q "public\storage" 2>nul
)

REM Create the symbolic link using mklink
REM /D creates a directory symbolic link
echo Creating symbolic link from public\storage to storage\app\public...
mklink /D "public\storage" "storage\app\public"

if %errorlevel% equ 0 (
    echo.
    echo SUCCESS: Storage link created successfully!
    echo.
) else (
    echo.
    echo ERROR: Failed to create storage link.
    echo.
    echo This command requires Administrator privileges.
    echo Please run this script as Administrator:
    echo 1. Right-click on this file
    echo 2. Select "Run as administrator"
    echo.
    pause
)
