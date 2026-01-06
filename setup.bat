@echo off
REM Setup script for Tech Phone Store (Windows)
echo Tech Phone Store - Setup Script
echo ==============================
echo.

REM Check if PHP is available
php -v >nul 2>&1
if errorlevel 1 (
    echo Error: PHP is not installed or not in PATH
    echo Please install PHP and add it to your system PATH
    pause
    exit /b 1
)

echo PHP is available
echo.

REM Run the PHP setup script
php setup.php

pause
