@echo off
echo ========================================
echo Tech Phone Store - Starting Server
echo ========================================
echo.
echo Server will be available at: http://localhost:8000
echo Press Ctrl+C to stop the server
echo.
echo ========================================
echo.

cd /d "%~dp0"
echo.
echo Starting PHP server...
echo API endpoints should work with .php extension
echo.
C:\xampp\php\php.exe -S localhost:8000
