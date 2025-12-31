# Database Cleanup Guide

Your database has orphaned InnoDB tablespace files that need to be manually cleaned up.

## Quick Fix Using phpMyAdmin (Recommended)

1. **Open phpMyAdmin** (usually at `http://localhost/phpmyadmin`)
2. **Select the `digicart` database** from the left sidebar
3. **Click "Operations" tab** at the top
4. **Click "Drop the database (DROP)"** button
5. Confirm the deletion
6. **Run the setup script again:**
   ```bash
   php backend/setup.php
   ```

## Alternative: Manual File System Cleanup

If you know where MySQL stores its data files (usually in `C:\xampp\mysql\data\` or `C:\wamp\bin\mysql\mysql[version]\data\`):

1. **Stop MySQL service** (if running as a service)
2. **Navigate to MySQL data directory**
3. **Delete the `digicart` folder** from the data directory
4. **Start MySQL service** again
5. **Run the setup script:**
   ```bash
   php backend/setup.php
   ```

## Using MySQL Command Line (If Available)

If you have MySQL command line tools accessible:

```bash
# Find MySQL bin directory (usually in C:\xampp\mysql\bin or similar)
cd C:\xampp\mysql\bin

# Connect to MySQL
mysql.exe -u root

# In MySQL prompt:
DROP DATABASE digicart;
CREATE DATABASE digicart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Then import schema
mysql.exe -u root digicart < "C:\Users\utsab\OneDrive\Desktop\hamrodigicart\hamrodigicart\backend\database\schema.sql"
```

## After Cleanup

Once the database is dropped and recreated, run:

```bash
php backend/setup.php
```

This will create all tables and insert default data.




