@echo off
REM Quick Start Script for Tech Phone Store
echo ========================================
echo Tech Phone Store - Quick Start
echo ========================================
echo.

REM Check for PHP
where php >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP is not found in PATH
    echo.
    echo Please install PHP or XAMPP:
    echo - XAMPP: https://www.apachefriends.org/
    echo - PHP: https://www.php.net/downloads.php
    echo.
    echo If you have XAMPP, PHP is at: C:\xampp\php\php.exe
    echo.
    pause
    exit /b 1
)

echo [OK] PHP found
php -v
echo.

REM Check for config file
if not exist "config\database.php" (
    echo [WARNING] config\database.php not found
    echo Please configure your database first
    echo.
    pause
)

REM Test database connection
echo Testing database connection...
php test-db.php
echo.

if %errorlevel% neq 0 (
    echo [WARNING] Database connection failed
    echo Please:
    echo 1. Make sure MySQL is running
    echo 2. Update config\database.php with your credentials
    echo 3. Import database\schema.sql
    echo.
    pause
)

echo ========================================
echo Starting PHP development server...
echo.
echo Server will be available at: http://localhost:8000
echo Press Ctrl+C to stop the server
echo ========================================
echo.

php -S localhost:8000
