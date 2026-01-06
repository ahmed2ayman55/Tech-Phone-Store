@echo off
REM Database Setup Script for Tech Phone Store
echo ========================================
echo Setting up database...
echo ========================================
echo.

REM Check if MySQL is running
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL is running
) else (
    echo [WARNING] MySQL might not be running
    echo Please start MySQL from XAMPP Control Panel
    echo.
    pause
)

echo.
echo Importing database schema...
echo.

REM Import database schema
cd /d "%~dp0"
C:\xampp\mysql\bin\mysql.exe -u root < "database\schema.sql"

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo Database setup completed successfully!
    echo ========================================
    echo.
    echo Testing connection...
    echo.
    C:\xampp\php\php.exe test-db.php
) else (
    echo.
    echo [ERROR] Database setup failed
    echo.
    echo Please check:
    echo 1. MySQL is running in XAMPP
    echo 2. You have permission to create databases
    echo 3. The schema.sql file exists
    echo.
)

pause
