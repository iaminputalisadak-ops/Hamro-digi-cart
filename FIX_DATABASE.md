# 🔧 Fix Database Connection Issue

The setup detected that the MySQL PDO driver is not enabled in PHP. Here's how to fix it:

## Quick Fix for Windows (XAMPP)

1. **Open `php.ini` file:**
   - Location: `C:\xampp\php\php.ini` (or your PHP installation path)
   - Find it: `php --ini` command will show the path

2. **Enable MySQL PDO extension:**
   - Find this line: `;extension=pdo_mysql`
   - Remove the semicolon: `extension=pdo_mysql`
   - Save the file

3. **Restart PHP server:**
   - Stop the current server (Ctrl+C)
   - Start again: `cd backend && php -S localhost:8000`

## Alternative: Install MySQL Extension

If using standalone PHP:

1. **Download MySQL extension DLL:**
   - For PHP 8.5: Download from https://pecl.php.net/package/pdo_mysql
   - Or use: `php -m` to see available extensions

2. **Enable in php.ini:**
   ```
   extension=pdo_mysql
   ```

## Verify Installation

Run this command:
```bash
php -m | findstr pdo_mysql
```

If you see `pdo_mysql`, it's enabled!

## After Fixing

1. **Create MySQL database:**
   ```sql
   CREATE DATABASE hamrodigicart;
   ```

2. **Import schema:**
   ```bash
   mysql -u root -p hamrodigicart < backend/database/schema.sql
   ```

3. **Configure database:**
   - Edit `backend/config/database.php`
   - Update username and password

4. **Run setup again:**
   ```bash
   cd backend
   php setup.php
   ```

## Current Status

✅ Backend server: Running on http://localhost:8000  
✅ Frontend server: Running on http://localhost:3000  
⚠️ Database: Needs MySQL PDO extension enabled  

Once database is configured, everything will work perfectly!





