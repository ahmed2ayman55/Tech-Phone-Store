@echo off
REM Simple Database Setup - Step by Step
echo ========================================
echo Tech Phone Store - Database Setup
echo ========================================
echo.

echo Step 1: Creating database...
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS tech_phone_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if %errorlevel% neq 0 (
    echo [ERROR] Failed to create database
    echo Make sure MySQL is running in XAMPP
    pause
    exit /b 1
)

echo [OK] Database created
echo.

echo Step 2: Importing schema...
C:\xampp\mysql\bin\mysql.exe -u root tech_phone_store < "database\schema.sql"

if %errorlevel% neq 0 (
    echo [ERROR] Failed to import schema
    pause
    exit /b 1
)

echo [OK] Schema imported
echo.

echo Step 3: Testing connection...
C:\xampp\php\php.exe test-db.php

echo.
echo ========================================
echo Setup complete!
echo ========================================
pause
