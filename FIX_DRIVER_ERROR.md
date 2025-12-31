# Fix "could not find driver" Error

## Problem
When accessing the admin panel, you get: "Database connection failed: could not find driver"

## Solution

The PHP built-in server needs to use the correct php.ini file with MySQL extensions enabled.

### Option 1: Use the Fixed Startup Script (Recommended)

1. **Stop your current PHP server** (if running)

2. **Use the fixed startup script:**
   ```bash
   start-backend-fixed.bat
   ```

   This script explicitly tells PHP to use the correct php.ini file.

### Option 2: Manual Start with Correct php.ini

1. Open terminal in the project root
2. Run:
   ```bash
   cd backend
   php -S localhost:8000 -c "C:\Users\utsab\Downloads\php-8.5.1-nts-Win32-vs17-x64\php.ini"
   ```

### Option 3: Verify Extensions are Loaded

1. **Check extensions in browser:**
   - Start the server
   - Visit: http://localhost:8000/check-extensions.php
   - This will show if extensions are loaded

2. **Or check phpinfo:**
   - Visit: http://localhost:8000/phpinfo.php
   - Search for "pdo_mysql" and "mysqli"
   - They should be listed under "Loaded Extensions"

### If Extensions Still Not Loading

1. **Verify php.ini settings:**
   - Open: `C:\Users\utsab\Downloads\php-8.5.1-nts-Win32-vs17-x64\php.ini`
   - Make sure these lines are NOT commented (no `;` at start):
     ```ini
     extension_dir = "ext"
     extension=mysqli
     extension=pdo_mysql
     ```

2. **Check extension files exist:**
   - Go to: `C:\Users\utsab\Downloads\php-8.5.1-nts-Win32-vs17-x64\ext\`
   - Verify these files exist:
     - `php_mysqli.dll`
     - `php_pdo_mysql.dll`

3. **Restart the PHP server** after making changes

## Quick Test

After starting the server with the fixed script:

1. Visit: http://localhost:8000/check-extensions.php
2. You should see: "✓ All MySQL extensions are loaded!"
3. Then try: http://localhost:8000/admin/login.php

## Default Login Credentials

- Username: `admin`
- Password: `admin123`

---

**Note:** The extensions are already enabled in your php.ini file. The issue is just making sure the PHP server uses that file when starting.





