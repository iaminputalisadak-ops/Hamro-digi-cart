@echo off
echo Starting PHP Backend Server with MySQL extensions...
echo.
cd backend
php -S localhost:8000 -c "C:\Users\utsab\Downloads\php-8.5.1-nts-Win32-vs17-x64\php.ini"
pause

