@echo off
echo Enabling OpenSSL in PHP configuration...
echo.

set PHP_INI=C:\Users\utsab\Downloads\php-8.5.1-nts-Win32-vs17-x64\php.ini

if not exist "%PHP_INI%" (
    echo ERROR: PHP ini file not found at: %PHP_INI%
    pause
    exit /b 1
)

echo Checking current OpenSSL configuration...

findstr /i "extension=openssl" "%PHP_INI%" >nul
if %errorlevel% equ 0 (
    echo OpenSSL extension is already enabled (or commented out).
    echo Checking if it's uncommented...
    
    findstr /i "^extension=openssl" "%PHP_INI%" >nul
    if %errorlevel% neq 0 (
        echo Uncommenting extension=openssl...
        powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=openssl', 'extension=openssl' | Set-Content '%PHP_INI%'"
        echo Done! OpenSSL extension enabled.
    ) else (
        echo OpenSSL extension is already enabled.
    )
) else (
    echo Adding extension=openssl...
    echo. >> "%PHP_INI%"
    echo ; Enable OpenSSL for SMTP/SSL support >> "%PHP_INI%"
    echo extension=openssl >> "%PHP_INI%"
    echo Done! OpenSSL extension added.
)

echo.
echo Checking extension_dir...
findstr /i "^extension_dir" "%PHP_INI%" >nul
if %errorlevel% neq 0 (
    echo Setting extension_dir...
    echo extension_dir = "ext" >> "%PHP_INI%"
    echo Done!
)

echo.
echo ========================================
echo Configuration updated!
echo.
echo IMPORTANT: You must restart your PHP server for changes to take effect.
echo Stop the server (Ctrl+C) and start it again.
echo ========================================
echo.
pause





