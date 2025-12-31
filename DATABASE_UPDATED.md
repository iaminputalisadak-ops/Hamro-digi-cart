# ✅ Database Name Updated

The database name has been changed from `hamrodigicart` to `digicart`.

## Changes Made

1. ✅ `backend/config/database.php` - Updated DB_NAME to 'digicart'
2. ✅ `backend/database/schema.sql` - Updated database name to 'digicart'

## Next Steps

### 1. Create the Database

If the database doesn't exist yet, create it:

```sql
CREATE DATABASE digicart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Import the Schema

```bash
mysql -u root -p digicart < backend/database/schema.sql
```

Or manually run the SQL in `backend/database/schema.sql`

### 3. Enable MySQL PDO Extension

The setup script shows that MySQL PDO driver is not enabled. To fix:

1. Find your `php.ini` file:
   ```bash
   php --ini
   ```

2. Open `php.ini` and find:
   ```ini
   ;extension=pdo_mysql
   ```

3. Remove the semicolon:
   ```ini
   extension=pdo_mysql
   ```

4. Save and restart PHP server

### 4. Run Setup Again

After enabling the extension:

```bash
cd backend
php setup.php
```

## Current Configuration

- **Database Name:** `digicart`
- **Database Host:** `localhost`
- **Database User:** `root`
- **Database Password:** (empty - update in `backend/config/database.php` if needed)

## Verify Connection

Once the PDO extension is enabled, the setup script will:
- ✅ Test database connection
- ✅ Create missing tables
- ✅ Set up default admin user
- ✅ Create default categories and pages

---

**Note:** The servers are still running. Once you enable the MySQL PDO extension and create the database, everything will work!





