@echo off
title Hamro Digi Cart - Application Launcher
color 0A
echo ========================================
echo   Hamro Digi Cart - Starting Application
echo ========================================
echo.

REM Check MySQL
echo [1/3] Checking MySQL...
netstat -ano | findstr ":3308" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo    ✓ MySQL is running on port 3308
) else (
    echo    ✗ MySQL is NOT running!
    echo    Please start MySQL from XAMPP Control Panel first!
    pause
    exit /b 1
)
echo.

REM Start Backend Server
echo [2/3] Starting Backend Server (port 8000)...
start "Backend Server" /D "%~dp0backend" cmd /k "echo Backend Server Running on http://0.0.0.0:8000 && echo Accessible from network: http://YOUR_IP:8000 && echo Press Ctrl+C to stop && php -S 0.0.0.0:8000 -c php.ini -t . router.php"
timeout /t 2 /nobreak >nul
echo    ✓ Backend server starting...
echo.

REM Start Frontend Server
echo [3/3] Starting Frontend Server (port 3000)...
start "Frontend Server" /D "%~dp0" cmd /k "echo Frontend Server Starting... && npm start"
timeout /t 3 /nobreak >nul
echo    ✓ Frontend server starting...
echo.

echo ========================================
echo   Application Started Successfully!
echo ========================================
echo.
echo Access your application:
echo   • Frontend:  http://localhost:3000
echo   • Backend:   http://localhost:8000/api
echo   • Admin:     http://localhost:8000/admin/login.php
echo.
echo Default Admin Credentials:
echo   Username: hamrodigicart1
echo   Password: admin123
echo.
echo NOTE: Two new windows opened for backend and frontend servers.
echo       Keep these windows open while using the application.
echo       Press Ctrl+C in those windows to stop the servers.
echo.
pause





