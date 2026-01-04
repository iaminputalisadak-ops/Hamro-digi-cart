@echo off
echo Starting PHP Backend Server...
echo.
echo Backend will be available at:
echo   Local:  http://localhost:8000
echo   Network: http://YOUR_IP:8000 (accessible from other devices)
echo Admin Panel: http://localhost:8000/admin/login.php
echo.
echo Press Ctrl+C to stop the server
echo.

REM Get the directory where this batch file is located
set "SCRIPT_DIR=%~dp0"
set "BACKEND_DIR=%SCRIPT_DIR%backend"

REM Change to backend directory
cd /d "%BACKEND_DIR%"

REM Check if router.php exists
if not exist "router.php" (
    echo ERROR: router.php not found in backend directory!
    echo Current directory: %CD%
    echo Backend directory: %BACKEND_DIR%
    pause
    exit /b 1
)

REM Start PHP server with router (accessible from network)
php -S 0.0.0.0:8000 -c php.ini -t . router.php

