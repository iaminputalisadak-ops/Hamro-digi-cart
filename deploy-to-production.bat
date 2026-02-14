@echo off
title Production Deployment Preparation
color 0A
echo ========================================
echo   Production Deployment Preparation
echo ========================================
echo.

echo This script will help prepare your website
echo for production deployment.
echo.

REM Check if .env.production exists
if exist ".env.production" (
    echo [INFO] .env.production file found
) else (
    echo [WARNING] .env.production file not found!
    echo.
    echo Creating .env.production template...
    (
        echo REACT_APP_API_URL=https://yourdomain.com/backend/api
        echo REACT_APP_SITE_URL=https://yourdomain.com
    ) > .env.production
    echo.
    echo ✓ Created .env.production
    echo.
    echo IMPORTANT: Edit .env.production and replace
    echo 'yourdomain.com' with your actual domain name!
    echo.
    pause
)

echo.
echo [1/3] Checking if build folder exists...
if exist "build" (
    echo    ⚠ Build folder already exists
    echo    It will be overwritten with new build.
    echo.
    choice /C YN /M "Continue with build"
    if errorlevel 2 exit /b 1
) else (
    echo    ✓ Ready for build
)

echo.
echo [2/3] Building React application for production...
echo    This may take a few minutes...
echo.
call npm run build

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ✗ Build failed! Please check for errors above.
    pause
    exit /b 1
)

echo.
echo [3/3] Verifying build output...
if exist "build\index.html" (
    echo    ✓ index.html found
) else (
    echo    ✗ index.html not found - build may have failed
)

if exist "build\static" (
    echo    ✓ static folder found
) else (
    echo    ✗ static folder not found - build may have failed
)

echo.
echo ========================================
echo   Build Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Edit .env.production with your domain
echo 2. Rebuild if you changed .env.production
echo 3. Upload build/ folder contents to public_html/
echo 4. Upload backend/ folder to public_html/backend/
echo 5. Configure database in cPanel
echo 6. Update backend/config/database.php
echo.
echo For detailed instructions, see:
echo   PRODUCTION_DEPLOYMENT_GUIDE.md
echo.
pause









