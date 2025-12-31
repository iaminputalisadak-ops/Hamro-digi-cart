@echo off
echo ========================================
echo Restarting PHP Backend Server...
echo ========================================
echo.

echo Stopping any existing PHP server on port 8000...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8000 ^| findstr LISTENING') do (
    echo Found process %%a, stopping...
    taskkill /F /PID %%a >nul 2>&1
)
timeout /t 2 /nobreak >nul

echo.
echo Starting PHP Backend Server with OpenSSL enabled...
echo.
echo Backend will be available at: http://localhost:8000
echo Admin Panel: http://localhost:8000/admin/login.php
echo.
echo Press Ctrl+C to stop the server
echo.

cd backend
php -S localhost:8000 -c php.ini router.php





