# 🚀 cPanel Deployment Guide for Hamro Digi Cart

This guide will help you deploy your React + PHP application to cPanel hosting.

---

## 📋 Prerequisites

Before deploying, make sure you have:
- ✅ cPanel access credentials
- ✅ Database credentials (host, name, username, password)
- ✅ Your domain name
- ✅ FTP/cPanel File Manager access
- ✅ Node.js installed locally (for building React app)

---

## 📁 Step 1: Build Your React Application

### 1.1 Create Production Build

On your local machine, run:

```bash
npm run build
```

This will create a `build` folder with optimized production files.

**Note:** The build process may take 2-5 minutes. You'll see output like:
```
Creating an optimized production build...
Compiled successfully!
```

---

## 📂 Step 2: Prepare Files for Upload

### 2.1 Files/Folders to Upload

You need to upload **TWO separate parts**:

#### **Part A: Frontend (React Build)**
Upload the contents of the `build` folder to your **public_html** directory:
```
public_html/
├── index.html
├── static/
│   ├── css/
│   ├── js/
│   └── media/
├── manifest.json
├── robots.txt
└── .htaccess (from public/.htaccess)
```

#### **Part B: Backend (PHP)**
Upload the entire `backend` folder to your **public_html** directory:
```
public_html/
├── backend/
│   ├── admin/
│   ├── api/
│   ├── config/
│   ├── database/
│   ├── uploads/
│   ├── router.php
│   ├── php.ini
│   └── ... (all backend files)
```

---

## ⚙️ Step 3: Configure Database

### 3.1 Create Database in cPanel

1. **Login to cPanel**
2. Go to **MySQL Databases** (or **MySQL Database Wizard**)
3. Create a new database: `your_domain_hamrodigicart` (or similar)
4. Create a new MySQL user
5. Add the user to the database with **ALL PRIVILEGES**
6. **Note down these credentials:**
   - Database Name
   - Database Username
   - Database Password
   - Database Host (usually `localhost` or `localhost:3306`)

### 3.2 Update Database Configuration

Edit `backend/config/database.php`:

```php
<?php
define('DB_HOST', 'localhost'); // Usually 'localhost' in cPanel
define('DB_PORT', '3306');      // Usually 3306 (default MySQL port)
define('DB_NAME', 'your_database_name'); // Your cPanel database name
define('DB_USER', 'your_database_user'); // Your cPanel database user
define('DB_PASS', 'your_database_password'); // Your cPanel database password
define('DB_CHARSET', 'utf8mb4');
```

**Important:** 
- Remove port number if cPanel doesn't use custom ports
- Database name format is usually: `cpanel_username_dbname`
- Database user format is usually: `cpanel_username_dbuser`

### 3.3 Import Database Schema

**Option A: Using phpMyAdmin (Recommended)**
1. Go to **phpMyAdmin** in cPanel
2. Select your database
3. Click **Import** tab
4. Choose `backend/database/schema.sql`
5. Click **Go**

**Option B: Using Setup Script**
1. Upload all backend files
2. Visit: `https://hamrodigicart.com/backend/setup.php`
3. The script will create tables and insert default data
4. Delete `setup.php` after successful setup for security

---

## 🔧 Step 4: Configure API Endpoints

### 4.1 Update React API Configuration

Before building, create a `.env` file in the root directory:

```env
REACT_APP_API_URL=https://hamrodigicart.com/api
REACT_APP_SITE_URL=https://hamrodigicart.com
```

**Or copy the provided `.env.production` file:**

```bash
cp .env.production .env
```

Then rebuild:
```bash
npm run build
```

**Note:** The API URL should be set via environment variables before building. The default in code will use `/api` which works if frontend and backend are on the same domain.

---

## 📝 Step 5: Configure .htaccess Files

### 5.1 Root .htaccess (for React Router)

In `public_html/.htaccess`, ensure you have:

```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /
  
  # Don't rewrite files or directories
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  
  # Don't rewrite backend folder
  RewriteCond %{REQUEST_URI} !^/backend
  
  # Rewrite everything else to index.html
  RewriteRule ^ index.html [L]
</IfModule>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Browser caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType application/json "access plus 0 seconds"
    ExpiresByType text/html "access plus 0 seconds"
</IfModule>
```

### 5.2 Backend .htaccess

In `public_html/backend/.htaccess`:

```apache
# Allow API access
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
    Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization"
</IfModule>

# Route API requests through router.php
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /backend/
    
    # Route /api/* requests to router.php
    RewriteCond %{REQUEST_URI} ^/backend/api/
    RewriteRule ^api/(.*)$ router.php [QSA,L]
    
    # Route /admin/* requests directly (no rewrite needed)
</IfModule>
```

---

## 📤 Step 6: Upload Files via cPanel File Manager

### 6.1 Upload Process

1. **Login to cPanel**
2. Open **File Manager**
3. Navigate to `public_html` folder
4. **Clear existing files** (if any) - make a backup first!

5. **Upload Frontend:**
   - Select all files from your local `build` folder
   - Upload to `public_html/`
   - Ensure `index.html` is in the root

6. **Upload Backend:**
   - Upload entire `backend` folder to `public_html/backend/`

7. **Set Permissions:**
   - `backend/uploads/` folder: **755** (or 775)
   - `backend/uploads/.htaccess`: **644**
   - All PHP files: **644**
   - All directories: **755**

---

## 🔒 Step 7: Security Configurations

### 7.1 Remove Development Files

After deployment, **DELETE** these files for security:
- `backend/setup.php`
- `backend/install.php`
- `backend/reset-admin-password.php`
- `backend/update-admin-username.php`
- `backend/check-admin.php`
- `backend/php.ini` (if not needed)
- Any `.env` files with sensitive data

### 7.2 Protect Sensitive Files

Create `public_html/backend/.htaccess` (if not exists):

```apache
# Protect config files
<FilesMatch "^(config|database)">
    Order allow,deny
    Deny from all
</FilesMatch>

# Prevent directory listing
Options -Indexes
```

---

## ✅ Step 8: Test Your Deployment

### 8.1 Test Checklist

1. **Frontend:**
   - Visit: `https://hamrodigicart.com`
   - Check if homepage loads
   - Test navigation

2. **API:**
   - Visit: `https://hamrodigicart.com/backend/api/products.php`
   - Should return JSON data

3. **Admin Panel:**
   - Visit: `https://hamrodigicart.com/backend/admin/login.php`
   - Login with: `hamrodigicart1` / `admin123`
   - Change password after first login!

4. **Database:**
   - Verify products load on homepage
   - Test creating a product in admin panel

5. **File Uploads:**
   - Test uploading product images
   - Check if files are saved in `backend/uploads/`

---

## 🐛 Common Issues & Solutions

### Issue 1: "404 Not Found" on React Routes

**Solution:** Check `.htaccess` rewrite rules are working. Some cPanel hosts require:
```apache
RewriteBase /
```
to be removed or adjusted.

### Issue 2: API Returns 404

**Solution:** 
- Check `backend/.htaccess` exists
- Verify `router.php` path is correct
- Check PHP version (should be 7.4+)

### Issue 3: Database Connection Failed

**Solution:**
- Verify database credentials in `backend/config/database.php`
- Check if database host is `localhost` (not `localhost:3306`)
- Ensure database user has proper permissions
- Some hosts use socket: `localhost:/path/to/mysql.sock`

### Issue 4: File Upload Not Working

**Solution:**
- Check `backend/uploads/` folder permissions (755 or 775)
- Verify PHP `upload_max_filesize` and `post_max_size` settings
- Check disk quota isn't full

### Issue 5: CORS Errors

**Solution:**
- Update `backend/.htaccess` with proper CORS headers
- Or configure in `backend/router.php`:
```php
header('Access-Control-Allow-Origin: https://hamrodigicart.com');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
```

### Issue 6: PHP Errors/Warnings

**Solution:**
- Check PHP version in cPanel (should be 7.4+)
- Enable error reporting temporarily:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```
- Check error logs in cPanel

---

## 📱 Step 9: SSL Certificate (Recommended)

1. In cPanel, go to **SSL/TLS Status**
2. Install **Let's Encrypt** SSL certificate (free)
3. Force HTTPS redirect in `.htaccess`:

```apache
# Force HTTPS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

---

## 📊 Step 10: Performance Optimization

### 10.1 Enable Gzip Compression

Already configured in `.htaccess` - verify it's working:
- Use: https://www.giftofspeed.com/gzip-test/

### 10.2 Optimize Images

Before uploading:
- Compress images using tools like TinyPNG
- Use WebP format when possible

### 10.3 Cache Static Assets

Already configured in `.htaccess` - browser caching is enabled

---

## 🔄 Step 11: Future Updates

When updating your site:

1. **Frontend Updates:**
   ```bash
   npm run build
   ```
   Upload new `build` folder contents to `public_html/`

2. **Backend Updates:**
   - Upload only changed files
   - Keep `backend/uploads/` folder intact (contains user uploads)

3. **Database Updates:**
   - Use phpMyAdmin to run SQL migrations
   - Always backup database before updates

---

## 📞 Support Checklist

If you encounter issues, check:

- [ ] PHP version is 7.4 or higher
- [ ] MySQL/MariaDB is enabled
- [ ] mod_rewrite is enabled (check in cPanel)
- [ ] File permissions are correct
- [ ] Database credentials are correct
- [ ] .htaccess files are uploaded
- [ ] API URL is correct in frontend
- [ ] SSL certificate is installed (if using HTTPS)
- [ ] Error logs in cPanel for specific errors

---

## 📝 Final Checklist

Before going live:

- [ ] Database is configured and imported
- [ ] All files are uploaded
- [ ] Permissions are set correctly
- [ ] .htaccess files are in place
- [ ] API endpoints are working
- [ ] Admin panel is accessible
- [ ] Test creating a product
- [ ] Test file uploads
- [ ] SSL certificate is installed
- [ ] Changed default admin password
- [ ] Removed development/setup files
- [ ] Tested on mobile devices
- [ ] Error reporting is disabled in production

---

## 🎉 Congratulations!

Your Hamro Digi Cart is now live on cPanel!

**Admin Login:**
- URL: `https://hamrodigicart.com/backend/admin/login.php`
- Username: `hamrodigicart1`
- Password: `admin123` (change immediately!)

**Frontend:**
- URL: `https://hamrodigicart.com`

---

## 🔐 Important Security Reminders

1. ✅ **Change default admin password** immediately after first login
2. ✅ **Delete setup/install scripts** after deployment
3. ✅ **Use HTTPS** (SSL certificate)
4. ✅ **Regular backups** of database and files
5. ✅ **Keep dependencies updated**
6. ✅ **Monitor error logs** regularly

---

**Need Help?** Check cPanel error logs and browser console for specific error messages.

