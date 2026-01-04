@echo off
echo ========================================
echo Hamro Digi Cart - Automated Setup
echo ========================================
echo.

REM Check if PHP is installed
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install PHP and add it to your system PATH
    pause
    exit /b 1
)

echo [1/3] Setting up backend...
cd backend
php setup.php
if %ERRORLEVEL% NEQ 0 (
    echo Backend setup failed!
    pause
    exit /b 1
)
cd ..

echo.
echo [2/3] Installing React dependencies...
call npm install
if %ERRORLEVEL% NEQ 0 (
    echo npm install failed!
    pause
    exit /b 1
)

echo.
echo [3/3] Creating environment file...
if not exist .env (
    echo REACT_APP_API_URL=http://localhost/backend/api > .env
    echo Created .env file with default API URL
) else (
    echo .env file already exists
)

echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo To start the application:
echo 1. Start backend: cd backend ^&^& php -S localhost:8000
echo 2. Start frontend: npm start
echo.
echo Admin Panel: http://localhost:8000/admin/login.php
echo Username: admin
echo Password: admin123
echo.
pause






