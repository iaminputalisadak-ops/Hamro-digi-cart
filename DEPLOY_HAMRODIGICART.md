# 🚀 Deploy hamrodigicart.com - Step by Step Guide

Your domain: **hamrodigicart.com**

---

## 📋 Pre-Deployment Checklist

- [ ] You have cPanel access for hamrodigicart.com
- [ ] Domain is pointing to your hosting server
- [ ] Database access is available
- [ ] File upload access (FTP/cPanel File Manager)

---

## Step 1: Build Production Files

### 1.1 Build the React App

Run this command in your project folder:

```bash
npm run build
```

**Wait for:** "Compiled successfully!" message

This creates a `build` folder with optimized production files.

### 1.2 Verify Build

Check that these files exist in `build/` folder:
- ✅ `index.html`
- ✅ `static/` folder (with CSS, JS, media)
- ✅ `.htaccess`

---

## Step 2: Set Up Database in cPanel

### 2.1 Create Database

1. **Login to cPanel** for hamrodigicart.com
2. Find **"MySQL Databases"** or **"MySQL Database Wizard"**
3. **Create Database:**
   - Database name: `yourname_hamrodigicart` (cPanel adds username prefix)
   - Click **"Create Database"**

### 2.2 Create Database User

1. Scroll to **"MySQL Users"**
2. **Create User:**
   - Username: `yourname_dbuser`
   - Password: **Create a strong password** (save it!)
   - Click **"Create User"**

### 2.3 Add User to Database

1. Scroll to **"Add User to Database"**
2. Select user and database
3. Click **"Add"**
4. **Grant ALL PRIVILEGES**
5. Click **"Make Changes"**

### 2.4 Import Database Schema

1. Go to **phpMyAdmin** in cPanel
2. Select your database from left sidebar
3. Click **"Import"** tab
4. Click **"Choose File"** → Select `backend/database/schema.sql`
5. Click **"Go"** to import

**📝 Save These Credentials:**
```
Database Name: _________________
Database User: _________________
Database Password: _________________
Database Host: localhost (usually)
```

---

## Step 3: Upload Files to cPanel

### 3.1 Upload Frontend Files

1. Open **cPanel File Manager**
2. Navigate to `public_html/` folder
3. **Upload ALL contents** of your local `build/` folder to `public_html/`

**Files to upload:**
- `index.html`
- `static/` folder (entire folder)
- `manifest.json`
- `robots.txt`
- `sitemap.xml`
- `.htaccess`

### 3.2 Upload Backend Files

1. In File Manager, create folder: `public_html/backend/` (if not exists)
2. Upload **ALL contents** of your local `backend/` folder to `public_html/backend/`

**Important folders:**
- `backend/api/`
- `backend/admin/`
- `backend/config/`
- `backend/database/`
- All PHP files

### 3.3 Create Uploads Folder & Set Permissions

1. Create folder: `public_html/backend/uploads/`
2. **Set Permissions:**
   - Right-click `uploads/` folder
   - Select **"Change Permissions"**
   - Set to **755** (or check all boxes except "Write" for Others)
   - Click **"Change Permissions"**

---

## Step 4: Configure Database Connection

### 4.1 Update database.php

1. In File Manager, navigate to `public_html/backend/config/`
2. Edit file: `database.php`
3. Update with your database credentials:

```php
<?php
define('DB_HOST', 'localhost');  // Usually 'localhost'
define('DB_NAME', 'yourname_hamrodigicart');  // Your actual database name
define('DB_USER', 'yourname_dbuser');  // Your actual database user
define('DB_PASS', 'your_actual_password');  // Your actual password
define('DB_CHARSET', 'utf8mb4');
?>
```

**Replace with actual values from Step 2.4**

---

## Step 5: Install SSL Certificate

### 5.1 Install SSL in cPanel

1. In cPanel, find **"SSL/TLS Status"** or **"Let's Encrypt SSL"**
2. Find your domain: **hamrodicart.com**
3. Click **"Run AutoSSL"** or **"Install"** or **"Issue SSL"**
4. Wait for SSL to install (usually automatic, takes 1-5 minutes)

### 5.2 Force HTTPS

1. Edit file: `public_html/.htaccess`
2. Find this section (should be at the bottom):

```apache
# Force HTTPS (uncomment if you have SSL certificate)
# <IfModule mod_rewrite.c>
#     RewriteEngine On
#     RewriteCond %{HTTPS} off
#     RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
# </IfModule>
```

3. **Uncomment it** (remove the `#` symbols):

```apache
# Force HTTPS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

4. **Save** the file

---

## Step 6: Rebuild with HTTPS (Important!)

Since we're using HTTPS, rebuild the app:

1. The `.env.production` file is already configured correctly
2. Run: `npm run build`
3. **Re-upload** the `build/` folder contents to `public_html/` (overwrite existing files)

---

## Step 7: Test Your Live Website

### 7.1 Test Homepage

Visit: **https://hamrodigicart.com**

Check:
- ✅ Page loads without errors
- ✅ Shows green padlock (SSL active)
- ✅ Navigation works
- ✅ Products display

### 7.2 Test Admin Panel

Visit: **https://hamrodigicart.com/backend/admin/login.php**

**Default Login:**
- Username: `hamrodigicart1`
- Password: `admin123`

⚠️ **IMPORTANT:** Change password immediately after login!

### 7.3 Test API

Visit: **https://hamrodigicart.com/backend/api/products**

Should show JSON data (product list).

### 7.4 Test Image Upload

1. Login to admin panel
2. Go to **Products** → **Add New Product**
3. Upload an image
4. Verify image displays correctly

---

## Step 8: Final Configuration

### 8.1 Change Admin Password

1. Login to admin panel
2. Go to **Settings**
3. Change password to a strong one

### 8.2 Update Website Settings

1. In admin panel, go to **Website Settings**
2. Update:
   - Website name
   - Logo
   - Favicon
   - Contact information
   - Social media links

### 8.3 Configure Email (Optional)

1. Go to **Settings** → **Email Settings**
2. Enter SMTP credentials:
   - Gmail: Use App Password (not regular password)
   - Other: Use provider's SMTP settings

### 8.4 Generate Sitemap

Visit: **https://hamrodigicart.com/backend/generate-sitemap.php**

This creates/updates `sitemap.xml` automatically.

---

## ✅ Final Checklist

- [ ] Website loads: https://hamrodigicart.com
- [ ] SSL certificate active (green padlock)
- [ ] Admin panel accessible: https://hamrodigicart.com/backend/admin/
- [ ] Can login to admin panel
- [ ] Password changed
- [ ] Products display correctly
- [ ] Image upload works
- [ ] Orders can be placed
- [ ] Search works
- [ ] All pages accessible
- [ ] Mobile view works

---

## 🚨 Troubleshooting

### Website Shows "404 Not Found"

**Fix:**
- Verify `.htaccess` file is in `public_html/`
- Check file permissions (should be 644)

### Images Not Loading

**Fix:**
- Check `backend/uploads/` folder exists
- Verify permissions are 755
- Check image paths in database

### Database Connection Error

**Fix:**
- Double-check credentials in `backend/config/database.php`
- Verify database user has ALL PRIVILEGES
- Check database host (might need full hostname)

### SSL Not Working

**Fix:**
- Wait a few minutes after installing SSL
- Clear browser cache
- Try incognito/private window
- Check SSL status in cPanel

---

## 🎉 Success!

Your website **https://hamrodigicart.com** is now LIVE and accessible to everyone worldwide! 🌍

---

## 📞 Quick Reference

- **Website**: https://hamrodigicart.com
- **Admin Panel**: https://hamrodigicart.com/backend/admin/login.php
- **API**: https://hamrodigicart.com/backend/api/
- **Default Admin Username**: hamrodigicart1
- **Default Admin Password**: admin123 (change immediately!)

---

**Need more help?** Check `PRODUCTION_DEPLOYMENT_GUIDE.md` for detailed instructions.








