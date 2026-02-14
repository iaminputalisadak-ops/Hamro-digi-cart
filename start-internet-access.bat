@echo off
title Hamro Digi Cart - Internet Access Setup
color 0B
echo ========================================
echo   Starting Internet Access Setup
echo ========================================
echo.

REM Check if ngrok is available
where ngrok >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Ngrok is not installed or not in PATH
    echo.
    echo Please install ngrok:
    echo 1. Download from: https://ngrok.com/download
    echo 2. Extract ngrok.exe to a folder
    echo 3. Add that folder to your system PATH
    echo    OR place ngrok.exe in this directory
    echo.
    pause
    exit /b 1
)

echo [1/4] Checking MySQL...
netstat -ano | findstr ":3308" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo    ✓ MySQL is running
) else (
    echo    ⚠ MySQL is NOT running!
    echo    Please start MySQL from XAMPP Control Panel
    echo.
)

echo.
echo [2/4] Starting Backend Server (port 8000)...
start "Backend Server" /D "%~dp0backend" cmd /k "echo Backend Server on http://0.0.0.0:8000 && php -S 0.0.0.0:8000 -c php.ini -t . router.php"
timeout /t 2 /nobreak >nul
echo    ✓ Backend server started

echo.
echo [3/4] Starting Frontend Server (port 3000)...
start "Frontend Server" /D "%~dp0" cmd /k "echo Frontend Server Starting... && npm start"
timeout /t 3 /nobreak >nul
echo    ✓ Frontend server starting

echo.
echo [4/4] Starting Ngrok Tunnels...
echo.
echo Starting Frontend Tunnel (Port 3000)...
start "Ngrok Frontend" cmd /k "title Ngrok - Frontend (Port 3000) && echo Frontend Tunnel - Port 3000 && echo. && ngrok http 3000"
timeout /t 1 /nobreak >nul

echo Starting Backend Tunnel (Port 8000)...
start "Ngrok Backend" cmd /k "title Ngrok - Backend (Port 8000) && echo Backend Tunnel - Port 8000 && echo. && ngrok http 8000"
timeout /t 1 /nobreak >nobreak

echo.
echo ========================================
echo   Setup Complete!
echo ========================================
echo.
echo ✓ All servers are starting...
echo.
echo IMPORTANT: Check the Ngrok windows for public URLs!
echo.
echo You'll see URLs like:
echo   Frontend: https://xxxx-xxxx.ngrok.io
echo   Backend:  https://yyyy-yyyy.ngrok.io
echo.
echo Share these URLs with anyone to access your website!
echo.
echo Press any key to continue...
pause >nul









