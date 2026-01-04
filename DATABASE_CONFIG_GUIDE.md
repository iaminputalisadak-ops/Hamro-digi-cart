# 🗄️ Database Configuration Guide

## 📍 Where to Change Database Settings

### **Main Database Configuration File:**
**File:** `backend/config/database.php`

---

## 🔧 Step-by-Step Instructions

### **For Local Development (XAMPP):**

Edit: `backend/config/database.php`

```php
<?php
// Database credentials
define('DB_HOST', 'localhost');      // Usually 'localhost'
define('DB_PORT', '3308');           // Your MySQL port (3306 for default, 3308 for XAMPP)
define('DB_NAME', 'digicart');       // Your database name
define('DB_USER', 'root');           // Your MySQL username
define('DB_PASS', '');               // Your MySQL password (empty for XAMPP default)
define('DB_CHARSET', 'utf8mb4');
```

**Common Local Settings:**
- **XAMPP:** Host: `localhost`, Port: `3306` or `3308`, User: `root`, Pass: `''` (empty)
- **WAMP:** Host: `localhost`, Port: `3306`, User: `root`, Pass: `''` (empty)
- **MAMP:** Host: `localhost`, Port: `8889`, User: `root`, Pass: `root`

---

### **For cPanel Deployment (Production):**

Edit: `backend/config/database.php`

**Before uploading to cPanel, change these values:**

```php
<?php
// Database credentials for cPanel
define('DB_HOST', 'localhost');      // Usually 'localhost' in cPanel (check with hosting)
define('DB_PORT', '3306');           // Usually 3306 (default MySQL port)
define('DB_NAME', 'cpanel_hamrodigicart');  // Your cPanel database name
define('DB_USER', 'cpanel_dbuser');         // Your cPanel database username
define('DB_PASS', 'your_secure_password');  // Your cPanel database password
define('DB_CHARSET', 'utf8mb4');
```

---

## 📝 How to Find cPanel Database Credentials

### **Step 1: Login to cPanel**

### **Step 2: Create Database**

1. Go to **"MySQL Databases"** or **"MySQL Database Wizard"**
2. **Create Database:**
   - Enter database name: `hamrodigicart` (or any name)
   - Click "Create Database"
   - **Note:** cPanel will add a prefix like `yourusername_`
   - **Full database name will be:** `yourusername_hamrodigicart`

3. **Create Database User:**
   - Enter username: `dbuser` (or any name)
   - Enter password: (create strong password)
   - Click "Create User"
   - **Full username will be:** `yourusername_dbuser`

4. **Add User to Database:**
   - Select the user
   - Select the database
   - Click "Add"
   - Grant **ALL PRIVILEGES**
   - Click "Make Changes"

### **Step 3: Note Down Credentials**

Write down these EXACT values:
- **Database Name:** `yourusername_hamrodigicart`
- **Database User:** `yourusername_dbuser`
- **Database Password:** `your_password`
- **Database Host:** Usually `localhost` (check in cPanel if different)

---

## 🔄 Update database.php File

### **Method 1: Edit Before Upload**

1. **Open:** `backend/config/database.php`
2. **Replace the values:**

```php
<?php
/**
 * Database Configuration
 */

// Database credentials
define('DB_HOST', 'localhost');                    // Your cPanel host (usually 'localhost')
define('DB_PORT', '3306');                         // Usually 3306
define('DB_NAME', 'yourusername_hamrodigicart');   // Your cPanel database name
define('DB_USER', 'yourusername_dbuser');          // Your cPanel database user
define('DB_PASS', 'your_password_here');           // Your cPanel database password
define('DB_CHARSET', 'utf8mb4');
```

3. **Save the file**
4. **Upload to cPanel**

### **Method 2: Edit After Upload**

1. **Upload files to cPanel**
2. **Login to cPanel File Manager**
3. **Navigate to:** `public_html/backend/config/`
4. **Edit:** `database.php`
5. **Update the values**
6. **Save**

---

## ⚠️ Important Notes

### **1. Database Host in cPanel:**
- Usually: `localhost`
- Sometimes: `localhost:3306`
- Rarely: `localhost:/path/to/mysql.sock`
- **Check with your hosting provider if connection fails**

### **2. Database Name Format:**
- cPanel adds your username as prefix
- Example: If username is `john` and you create `hamrodigicart`
- **Full name:** `john_hamrodigicart`

### **3. Database User Format:**
- Same as database name
- Example: `john_dbuser`

### **4. Port Number:**
- Usually `3306` (default MySQL)
- Some hosts use custom ports
- **If using custom port, include in host:** `localhost:3307`
- Or keep host as `localhost` and change port to `3307`

### **5. Security:**
- Never commit `database.php` to public repositories
- Keep database password secure
- Use strong passwords

---

## 🧪 Test Database Connection

### **After Updating, Test Connection:**

1. **Via Browser:**
   - Visit: `https://yourdomain.com/backend/setup.php`
   - Should show database connection success

2. **Via Admin Panel:**
   - Visit: `https://yourdomain.com/backend/admin/login.php`
   - Try to login
   - If database connection fails, you'll see an error

3. **Via phpMyAdmin:**
   - Login to cPanel → phpMyAdmin
   - Check if your database exists
   - Check if tables were created

---

## 🔍 Troubleshooting Database Connection

### **Error: "Database connection failed"**

**Check:**
1. ✅ Database name is correct (including cPanel prefix)
2. ✅ Database username is correct (including cPanel prefix)
3. ✅ Database password is correct
4. ✅ Database host is correct (`localhost` usually)
5. ✅ Database exists in cPanel
6. ✅ User has permissions on database
7. ✅ Database was imported/setup completed

### **Error: "Access denied for user"**

**Solutions:**
- Check username includes cPanel prefix
- Verify password is correct
- Ensure user is added to database with ALL PRIVILEGES

### **Error: "Unknown database"**

**Solutions:**
- Check database name includes cPanel prefix
- Verify database was created in cPanel
- Import schema.sql if tables don't exist

---

## 📋 Quick Reference

| Setting | Local (XAMPP) | cPanel (Production) |
|---------|--------------|---------------------|
| **DB_HOST** | `localhost` | `localhost` |
| **DB_PORT** | `3306` or `3308` | `3306` |
| **DB_NAME** | `digicart` | `yourusername_hamrodigicart` |
| **DB_USER** | `root` | `yourusername_dbuser` |
| **DB_PASS** | `''` (empty) | `your_password` |

---

## ✅ Checklist

- [ ] Database created in cPanel
- [ ] Database user created in cPanel
- [ ] User added to database with ALL PRIVILEGES
- [ ] Credentials noted down
- [ ] `backend/config/database.php` updated
- [ ] File uploaded to cPanel
- [ ] Database connection tested
- [ ] Schema imported successfully

---

## 📁 File Location Summary

```
backend/
└── config/
    └── database.php    ← CHANGE DATABASE SETTINGS HERE
```

**This is the ONLY file you need to edit for database configuration!**








