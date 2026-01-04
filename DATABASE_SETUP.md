# MySQL Database Setup Guide

## Current Database Configuration

The database is configured in `backend/config/database.php`:
- **Host**: localhost
- **Port**: 3306
- **Database Name**: digicart
- **Username**: root
- **Password**: (empty - default for XAMPP)

## Step 1: Start MySQL Server

Since you have XAMPP installed, you need to start MySQL manually:

### Method 1: Using XAMPP Control Panel (Recommended)

1. **Open XAMPP Control Panel**:
   - Navigate to `C:\xampp\`
   - Double-click `xampp-control.exe`
   - Or search for "XAMPP Control Panel" in Windows Start Menu

2. **Start MySQL**:
   - In the XAMPP Control Panel, find "MySQL" in the list
   - Click the **"Start"** button next to MySQL
   - Wait until the status shows "Running" (it will turn green)

3. **Verify MySQL is Running**:
   - You should see "Running" status in green
   - The port should show "3306"

### Method 2: Using Command Line (Alternative)

You can also try running:
```batch
C:\xampp\mysql_start.bat
```

## Step 2: Verify Database Connection

After starting MySQL, run the setup script to verify everything works:

```batch
cd backend
php setup.php
```

This script will:
- ✓ Test the database connection
- ✓ Check if the database and tables exist
- ✓ Create the database and tables if needed
- ✓ Set up the default admin user

## Step 3: Create Database (if needed)

If the database `digicart` doesn't exist, the setup script will create it automatically.

Alternatively, you can create it manually:

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "New" to create a database
3. Enter database name: `digicart`
4. Select Collation: `utf8mb4_unicode_ci`
5. Click "Create"

## Troubleshooting

### MySQL Won't Start

If MySQL fails to start in XAMPP:

1. **Check if port 3306 is in use**:
   ```batch
   netstat -ano | findstr ":3306"
   ```
   If something is using port 3306, you may need to:
   - Stop the other MySQL service
   - Or change the port in XAMPP MySQL configuration

2. **Check XAMPP MySQL Error Log**:
   - Look in `C:\xampp\mysql\data\` for error logs
   - Or check the XAMPP Control Panel logs

3. **Restart XAMPP**:
   - Close XAMPP Control Panel completely
   - Run it as Administrator (Right-click → Run as administrator)
   - Try starting MySQL again

### Database Connection Error

If you get "Database connection failed":

1. Verify MySQL is running (should show green in XAMPP Control Panel)
2. Check database credentials in `backend/config/database.php`
3. Make sure the database `digicart` exists
4. Verify MySQL port is 3306 (check in XAMPP Control Panel)

### Change Database Password

If your MySQL root user has a password:

1. Edit `backend/config/database.php`
2. Update the `DB_PASS` constant:
   ```php
   define('DB_PASS', 'your_password_here');
   ```

## Quick Start Checklist

- [ ] Start MySQL from XAMPP Control Panel
- [ ] Verify MySQL is running (green status)
- [ ] Run `php backend/setup.php` to verify connection
- [ ] Database `digicart` should be created automatically
- [ ] All tables should be created
- [ ] Default admin user should be created (username: `admin`, password: `admin123`)

## After Setup

Once MySQL is running and the database is set up:

1. Your backend server should connect successfully
2. The frontend should be able to fetch data from the API
3. You can access the admin panel at: http://localhost:8000/admin/login.php






