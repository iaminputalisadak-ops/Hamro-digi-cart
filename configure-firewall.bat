@echo off
title Configure Firewall for Internet Access
color 0E

echo ========================================
echo   Configuring Windows Firewall
echo   for Internet Access
echo ========================================
echo.
echo This script will add firewall rules to allow
echo incoming connections on ports 3000 and 8000.
echo.
echo NOTE: This requires Administrator privileges.
echo.
pause

echo.
echo [1/2] Adding rule for Frontend (Port 3000)...
netsh advfirewall firewall add rule name="HamroDigiCart Frontend Port 3000" dir=in action=allow protocol=TCP localport=3000
if %ERRORLEVEL% EQU 0 (
    echo    ✓ Frontend port 3000 allowed
) else (
    echo    ✗ Failed to add rule. Make sure you're running as Administrator!
)

echo.
echo [2/2] Adding rule for Backend (Port 8000)...
netsh advfirewall firewall add rule name="HamroDigiCart Backend Port 8000" dir=in action=allow protocol=TCP localport=8000
if %ERRORLEVEL% EQU 0 (
    echo    ✓ Backend port 8000 allowed
) else (
    echo    ✗ Failed to add rule. Make sure you're running as Administrator!
)

echo.
echo ========================================
if %ERRORLEVEL% EQU 0 (
    echo   Firewall Configuration Complete!
    echo ========================================
    echo.
    echo Your website is now ready for internet access!
    echo.
) else (
    echo   Configuration Failed
    echo ========================================
    echo.
    echo Please run this script as Administrator:
    echo 1. Right-click this file
    echo 2. Select "Run as administrator"
    echo.
)
pause









