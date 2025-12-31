# Database Import Guide

Your database password has been updated in `backend/config/database.php` ✅

## Option 1: Using phpMyAdmin (Easiest)

1. Open phpMyAdmin in your browser (usually http://localhost/phpmyadmin)
2. Select the `digicart` database from the left sidebar
3. Click on the "Import" tab
4. Click "Choose File" and select: `backend/database/schema.sql`
5. Click "Go" to import

## Option 2: Using MySQL Command Line

If MySQL is installed, find the MySQL bin directory (usually in `C:\xampp\mysql\bin` or `C:\wamp\bin\mysql\mysql[version]\bin`) and run:

```bash
# Navigate to MySQL bin directory
cd C:\xampp\mysql\bin

# Import schema
mysql.exe -u root -putsab12@ digicart < "C:\Users\utsab\OneDrive\Desktop\hamrodigicart\hamrodigicart\backend\database\schema.sql"
```

Or if MySQL is in your PATH:
```bash
mysql -u root -putsab12@ digicart < backend/database/schema.sql
```

## Option 3: Enable PHP MySQL Extensions

1. Open your `php.ini` file (find it with: `php --ini`)
2. Find these lines and remove the semicolon (;) at the beginning:
   ```ini
   ;extension=pdo_mysql
   ;extension=mysqli
   ```
   Should become:
   ```ini
   extension=pdo_mysql
   extension=mysqli
   ```
3. Restart your web server or PHP
4. Then run: `php backend/setup.php`

## Option 4: Manual SQL Execution

1. Open MySQL Workbench or any MySQL client
2. Connect to your database (username: root, password: utsab12@)
3. Select the `digicart` database
4. Open `backend/database/schema.sql` in a text editor
5. Copy and paste the SQL statements into MySQL Workbench
6. Execute them

## After Import

Once the schema is imported, you can:

1. **Test the setup:**
   ```bash
   php backend/setup.php
   ```

2. **Start the backend server:**
   ```bash
   cd backend
   php -S localhost:8000
   ```

3. **Start the frontend:**
   ```bash
   npm start
   ```

4. **Access admin panel:**
   - URL: http://localhost:8000/admin/login.php
   - Username: `admin`
   - Password: `admin123`

---

**Note:** The database password has been updated to `utsab12@` in `backend/config/database.php`





