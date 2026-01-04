# .htaccess Configuration Files

This document contains the .htaccess configurations needed for cPanel deployment.

---

## 📄 1. Root .htaccess (public_html/.htaccess)

Place this file in your `public_html` directory (root of your website):

```apache
# Enable Rewrite Engine
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Don't rewrite files or directories that exist
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Don't rewrite backend folder
    RewriteCond %{REQUEST_URI} !^/backend
    
    # Don't rewrite API requests
    RewriteCond %{REQUEST_URI} !^/api
    
    # Rewrite everything else to index.html (for React Router)
    RewriteRule ^ index.html [L]
</IfModule>

# Enable Gzip Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE application/json
</IfModule>

# Browser Caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType application/x-javascript "access plus 1 month"
    ExpiresByType text/html "access plus 1 hour"
    ExpiresByType application/json "access plus 0 seconds"
    ExpiresDefault "access plus 2 days"
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Force HTTPS (uncomment after SSL is installed)
# <IfModule mod_rewrite.c>
#     RewriteEngine On
#     RewriteCond %{HTTPS} off
#     RewriteRule ^(.*)$ https://hamrodigicart.com%{REQUEST_URI} [L,R=301]
# </IfModule>
```

---

## 📄 2. Backend .htaccess (public_html/backend/.htaccess)

Place this file in your `backend` directory:

```apache
# Enable Rewrite Engine
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /backend/
    
    # Route API requests through router.php
    RewriteCond %{REQUEST_URI} ^/backend/api/
    RewriteRule ^api/(.*)$ router.php [QSA,L]
    
    # Allow direct access to admin files
    # (admin files are accessed directly, no rewrite needed)
</IfModule>

# CORS Headers for API
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
    Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"
    Header set Access-Control-Allow-Credentials "true"
</IfModule>

# Handle OPTIONS preflight requests
<IfModule mod_rewrite.c>
    RewriteCond %{REQUEST_METHOD} OPTIONS
    RewriteRule ^(.*)$ router.php [QSA,L]
</IfModule>

# Prevent directory listing
Options -Indexes

# Protect sensitive files and directories
<FilesMatch "^(\.htaccess|\.env|config\.php|database\.php)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Prevent access to config and database folders
<IfModule mod_rewrite.c>
    RewriteRule ^config/ - [F,L]
    RewriteRule ^database/ - [F,L]
</IfModule>

# Increase upload limits (if allowed by hosting)
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300
php_value max_input_time 300
```

---

## 📄 3. Uploads .htaccess (public_html/backend/uploads/.htaccess)

Place this file in your `backend/uploads` directory:

```apache
# Allow image files to be served
<FilesMatch "\.(jpg|jpeg|png|gif|webp|svg)$">
    Order allow,deny
    Allow from all
</FilesMatch>

# Prevent PHP execution in uploads directory
<FilesMatch "\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Prevent access to .htaccess files
<FilesMatch "^(\.htaccess|\.htpasswd)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Enable CORS for images (if needed)
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
</IfModule>
```

---

## ⚠️ Important Notes

1. **RewriteBase**: Some cPanel hosts require `RewriteBase /` to be removed or adjusted. If you get 404 errors, try removing the `RewriteBase` line.

2. **PHP Settings**: The PHP settings in backend/.htaccess (`php_value`) may not work on all hosts. If you get errors, remove those lines and configure PHP settings via cPanel or `php.ini`.

3. **CORS Headers**: The `Access-Control-Allow-Origin: *` allows all domains. For better security, replace `*` with your domain:
   ```apache
   Header set Access-Control-Allow-Origin "https://hamrodigicart.com"
   ```

4. **HTTPS Redirect**: Uncomment the HTTPS redirect section in root .htaccess only after you've installed an SSL certificate.

5. **File Permissions**: After uploading .htaccess files, set permissions to **644**.

---

## 🧪 Testing .htaccess Configuration

1. **Test React Router:**
   - Visit: `https://yourdomain.com/product/1`
   - Should load without 404 error

2. **Test API:**
   - Visit: `https://yourdomain.com/backend/api/products.php`
   - Should return JSON data

3. **Test Admin Panel:**
   - Visit: `https://yourdomain.com/backend/admin/login.php`
   - Should load login page

4. **Test File Uploads:**
   - Visit: `https://yourdomain.com/backend/uploads/filename.png`
   - Should display the image

---

## 🔧 Troubleshooting

### Issue: 500 Internal Server Error

**Possible Causes:**
- Syntax error in .htaccess
- mod_rewrite not enabled
- PHP settings not allowed

**Solution:**
- Check error logs in cPanel
- Temporarily rename .htaccess to .htaccess_backup
- Test if site works without .htaccess
- Enable mod_rewrite in cPanel

### Issue: 404 on All Routes

**Solution:**
- Remove or adjust `RewriteBase` line
- Check if mod_rewrite is enabled
- Verify file paths are correct

### Issue: API Returns 404

**Solution:**
- Check `backend/.htaccess` exists
- Verify RewriteRule is correct
- Check if `router.php` exists in backend folder

---

**Remember:** Always backup your .htaccess files before making changes!

