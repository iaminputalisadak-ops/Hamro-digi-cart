@echo off
echo ========================================
echo Hamro Digi Cart - Status Check
echo ========================================
echo.

echo [1/5] Checking MySQL Status...
netstat -ano | findstr ":3306" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo    ✓ MySQL is RUNNING on port 3306
    set MYSQL_RUNNING=1
) else (
    echo    ✗ MySQL is NOT RUNNING
    echo    Please start MySQL from XAMPP Control Panel
    set MYSQL_RUNNING=0
)
echo.

echo [2/5] Checking PHP...
php --version >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    php --version | findstr /C:"PHP"
    echo    ✓ PHP is installed
) else (
    echo    ✗ PHP is NOT installed or not in PATH
)
echo.

echo [3/5] Checking Node.js...
node --version >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo    ✓ Node.js version:
    node --version
) else (
    echo    ✗ Node.js is NOT installed or not in PATH
)
echo.

echo [4/5] Checking Frontend Dependencies...
cd backend\..\
if exist "node_modules" (
    echo    ✓ Frontend dependencies are installed
) else (
    echo    ✗ Frontend dependencies are NOT installed
    echo    Run: npm install
)
echo.

echo [5/5] Testing Database Connection...
if "%MYSQL_RUNNING%"=="1" (
    cd backend
    php setup.php 2>&1 | findstr /C:"connection successful" >nul
    if %ERRORLEVEL% EQU 0 (
        echo    ✓ Database connection successful!
        cd ..\
        echo.
        echo ========================================
        echo Status: READY TO RUN
        echo ========================================
        echo.
        echo Your servers should be running:
        echo   - Backend: http://localhost:8000
        echo   - Frontend: http://localhost:3000
        echo   - Admin Panel: http://localhost:8000/admin/login.php
        echo.
    ) else (
        cd ..\
        echo    ⚠ Database connection failed
        echo    Run: php backend\setup.php
        echo.
        echo ========================================
        echo Status: NEEDS SETUP
        echo ========================================
    )
) else (
    echo    ⚠ Skipped - MySQL is not running
    echo.
    echo ========================================
    echo Status: MYSQL NOT RUNNING
    echo ========================================
    echo.
    echo To start MySQL:
    echo   1. Open XAMPP Control Panel
    echo   2. Click "Start" next to MySQL
    echo   3. Wait for green "Running" status
    echo   4. Run this check again
)
echo.
pause


















