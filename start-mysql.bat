@echo off
echo ========================================
echo Starting MySQL (XAMPP)
echo ========================================
echo.

REM Check if XAMPP MySQL is installed
if not exist "C:\xampp\mysql_start.bat" (
    echo ERROR: XAMPP MySQL not found at C:\xampp
    echo.
    echo Please start MySQL manually:
    echo 1. Open XAMPP Control Panel (xampp-control.exe)
    echo 2. Click "Start" next to MySQL
    echo.
    pause
    exit /b 1
)

REM Start MySQL using XAMPP's batch file
echo Starting MySQL service...
call "C:\xampp\mysql_start.bat"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo MySQL started successfully!
    echo Waiting 3 seconds for MySQL to initialize...
    timeout /t 3 /nobreak >nul
    echo.
    echo MySQL should now be running on port 3306
) else (
    echo.
    echo Failed to start MySQL automatically.
    echo.
    echo Please start MySQL manually:
    echo 1. Open XAMPP Control Panel
    echo 2. Click "Start" next to MySQL
    echo.
)

pause


















