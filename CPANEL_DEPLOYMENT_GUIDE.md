# 🚀 Step-by-Step cPanel Deployment Guide

## ✅ Pre-Deployment Verification

Everything is correct! The favicon feature has been successfully added and the build is ready for deployment.

---

## 📋 Quick Checklist

- ✅ React build compiled successfully
- ✅ Favicon feature code added
- ✅ Backend API updated
- ✅ Frontend component updated
- ✅ All files are ready

---

## 🎯 Step-by-Step Deployment to cPanel

### **STEP 1: Update Environment Variables (Before Building)**

1. Create or edit `.env` file in your project root:
```env
REACT_APP_SITE_URL=https://yourdomain.com
REACT_APP_API_URL=https://yourdomain.com/backend/api
```

2. **Important:** Replace `yourdomain.com` with your actual domain name.

---

### **STEP 2: Build React Application**

In your project folder, run:
```bash
npm run build
```

This creates a `build` folder with all production files.

**Wait for:** "Compiled successfully!" message

---

### **STEP 3: Prepare Files for Upload**

You need to upload **TWO parts**:

#### **Part A: Frontend Files** (from `build` folder)
- `index.html`
- `static/` folder (contains CSS, JS, media)
- `manifest.json`
- `robots.txt`
- `sitemap.xml`
- `.htaccess` (create this if it doesn't exist - see Step 5)

#### **Part B: Backend Files** (entire `backend` folder)
- `backend/` folder with all its contents

---

### **STEP 4: Create Database in cPanel**

1. **Login to cPanel**
2. Find **"MySQL Databases"** or **"MySQL Database Wizard"**
3. **Create a new database:**
   - Database name: `yourname_hamrodigicart` (or any name)
   - Click "Create Database"
4. **Create a new MySQL user:**
   - Username: `yourname_dbuser` (or any name)
   - Password: (create a strong password)
   - Click "Create User"
5. **Add user to database:**
   - Select the user
   - Select the database
   - Click "Add"
   - **Grant ALL PRIVILEGES**
   - Click "Make Changes"

**📝 IMPORTANT:** Write down these credentials:
- Database Name: `________________`
- Database User: `________________`
- Database Password: `________________`
- Database Host: Usually `localhost` (confirm in cPanel)

---

### **STEP 5: Create/Update .htaccess Files**

#### **A. Root .htaccess** (for `public_html/.htaccess`)

Create this file in your `build` folder or upload it separately:

```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /
  
  # Don't rewrite files or directories
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  
  # Don't rewrite backend folder
  RewriteCond %{REQUEST_URI} !^/backend
  
  # Rewrite everything else to index.html (for React Router)
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

# Force HTTPS (uncomment if you have SSL)
# <IfModule mod_rewrite.c>
#     RewriteEngine On
#     RewriteCond %{HTTPS} off
#     RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
# </IfModule>
```

#### **B. Backend .htaccess** (for `public_html/backend/.htaccess`)

```apache
# Allow API access (CORS)
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
</IfModule>

# Prevent directory listing
Options -Indexes

# Protect config files (optional but recommended)
<FilesMatch "^(config|database)">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

### **STEP 6: Configure Database Connection**

1. **Edit `backend/config/database.php`** before uploading:

```php
<?php
define('DB_HOST', 'localhost'); // Usually 'localhost' in cPanel
define('DB_PORT', '3306');      // Usually 3306 (default)
define('DB_NAME', 'yourname_hamrodigicart'); // Your database name from Step 4
define('DB_USER', 'yourname_dbuser'); // Your database user from Step 4
define('DB_PASS', 'your_password_here'); // Your database password from Step 4
define('DB_CHARSET', 'utf8mb4');

// ... rest of the file stays the same
```

2. **Save the file** after updating with your actual database credentials.

---

### **STEP 7: Upload Files via cPanel File Manager**

#### **Option A: Using cPanel File Manager (Recommended)**

1. **Login to cPanel**
2. Open **"File Manager"**
3. Navigate to `public_html` folder
4. **⚠️ Backup existing files first** (if any)

5. **Upload Frontend Files:**
   - Select all files and folders from your local `build` folder
   - Upload to `public_html/`
   - Ensure `index.html` is directly in `public_html/`

6. **Upload Backend Folder:**
   - Upload entire `backend` folder
   - Extract if uploaded as ZIP
   - Should be at `public_html/backend/`

7. **Set Permissions:**
   - Right-click `backend/uploads/` folder → Change Permissions → `755`
   - Right-click files → Change Permissions → `644`
   - Right-click folders → Change Permissions → `755`

#### **Option B: Using FTP (FileZilla or similar)**

1. Connect via FTP using cPanel FTP credentials
2. Navigate to `public_html/`
3. Upload files same way as above

---

### **STEP 8: Import Database**

#### **Method 1: Using phpMyAdmin (Easiest)**

1. **Login to cPanel**
2. Open **"phpMyAdmin"**
3. Click on your database name (left sidebar)
4. Click **"Import"** tab
5. Click **"Choose File"**
6. Select `backend/database/schema.sql` from your local project
7. Click **"Go"** at the bottom
8. Wait for "Import has been successfully finished" message

#### **Method 2: Using Setup Script (Alternative)**

1. Upload all files first
2. Visit: `https://yourdomain.com/backend/setup.php`
3. Follow the setup wizard
4. **⚠️ Delete `setup.php` after setup for security**

---

### **STEP 9: Test Your Deployment**

#### **Test 1: Frontend**
- Visit: `https://yourdomain.com`
- Should load homepage
- Check browser console for errors (F12)

#### **Test 2: API**
- Visit: `https://yourdomain.com/backend/api/products.php`
- Should return JSON data (not an error page)

#### **Test 3: Admin Panel**
- Visit: `https://yourdomain.com/backend/admin/login.php`
- Login with:
  - Username: `hamrodigicart1`
  - Password: `admin123`
- **⚠️ Change password immediately after first login!**

#### **Test 4: Favicon Feature**
- Login to admin panel
- Go to: Website Settings
- Find "Favicon" field
- Upload a favicon image
- Click "Save All Settings"
- Refresh frontend homepage
- Check browser tab - favicon should update

---

### **STEP 10: Security Hardening (Important!)**

**Delete these files after setup (for security):**

1. `backend/setup.php`
2. `backend/install.php`
3. `backend/reset-admin-password.php`
4. `backend/update-admin-username.php`
5. `backend/check-admin.php`

**How to delete:**
- Via File Manager: Right-click → Delete
- Via FTP: Delete the files

---

### **STEP 11: SSL Certificate (Recommended)**

1. In cPanel, find **"SSL/TLS Status"**
2. Install **"Let's Encrypt"** SSL (free)
3. Force HTTPS by uncommenting this in root `.htaccess`:
```apache
# Force HTTPS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

---

## 🔧 Troubleshooting Common Issues

### **Issue: "404 Not Found" on React Routes**

**Solution:**
- Check if root `.htaccess` file exists
- Verify RewriteEngine is enabled (contact hosting support if needed)
- Check file permissions (should be 644 for files, 755 for folders)

### **Issue: API Returns 404**

**Solution:**
- Verify `backend/.htaccess` exists
- Check if `mod_rewrite` is enabled (contact hosting support)
- Verify PHP version (should be 7.4+)

### **Issue: Database Connection Failed**

**Solution:**
- Double-check database credentials in `backend/config/database.php`
- Verify database host is `localhost` (not `localhost:3306`)
- Check if database user has proper permissions
- Some hosts use socket: `localhost:/path/to/mysql.sock` (check with hosting support)

### **Issue: File Upload Not Working**

**Solution:**
- Check `backend/uploads/` folder permissions (should be 755 or 775)
- Verify PHP `upload_max_filesize` settings (check in cPanel PHP Settings)
- Check disk quota isn't full

### **Issue: Favicon Not Updating**

**Solution:**
- Clear browser cache (Ctrl+F5 or Ctrl+Shift+R)
- Check if favicon URL is accessible directly
- Verify settings are saved in database (check via phpMyAdmin)

---

## 📝 Post-Deployment Checklist

- [ ] Frontend loads correctly
- [ ] API endpoints work
- [ ] Admin panel accessible
- [ ] Can login to admin panel
- [ ] Changed default admin password
- [ ] Can create/edit products
- [ ] File uploads work
- [ ] Favicon feature works
- [ ] SSL certificate installed (if applicable)
- [ ] Development files deleted
- [ ] Database imported successfully
- [ ] Test on mobile devices

---

## 🔄 How to Update in Future

### **When updating frontend:**
1. Make changes to React code
2. Update `.env` with production URLs
3. Run: `npm run build`
4. Upload new `build` folder contents to `public_html/`

### **When updating backend:**
1. Make changes to PHP files
2. Upload only changed files to `public_html/backend/`
3. Keep `backend/uploads/` folder intact (contains user data)

### **When updating database:**
1. Backup database first (via phpMyAdmin → Export)
2. Run SQL migrations if needed
3. Test in staging environment first if possible

---

## 📞 Need Help?

If you encounter issues:
1. Check cPanel error logs
2. Check browser console (F12)
3. Verify file permissions
4. Contact your hosting support for server-side issues

---

## ✅ Final Checklist Before Going Live

- [ ] All files uploaded
- [ ] Database configured and imported
- [ ] .htaccess files in place
- [ ] File permissions set correctly
- [ ] Database credentials updated
- [ ] Environment variables updated
- [ ] SSL certificate installed
- [ ] Default password changed
- [ ] Development files deleted
- [ ] Tested all features
- [ ] Tested on mobile
- [ ] Favicon feature tested

---

## 🎉 Congratulations!

Your Hamro Digi Cart with Favicon feature is now live on cPanel!

**Admin Login:**
- URL: `https://yourdomain.com/backend/admin/login.php`
- Username: `hamrodigicart1`
- Password: `admin123` (change immediately!)

**Frontend:**
- URL: `https://yourdomain.com`

**Favicon Settings:**
- Admin → Website Settings → Favicon field

---

**Everything is correct and ready for deployment! 🚀**










