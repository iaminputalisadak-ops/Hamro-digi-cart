# 🚀 Production Deployment Guide - Make Your Website Live

This guide will help you deploy your website to make it accessible to everyone on the internet.

## 📋 Prerequisites

Before deploying, make sure you have:
- ✅ A domain name (e.g., hamrodigicart.com)
- ✅ A web hosting account with cPanel (or similar)
- ✅ Database access (MySQL)
- ✅ File upload access (FTP/cPanel File Manager)

---

## 🎯 Step 1: Prepare Production Build

### 1.1 Create `.env.production` file

Create a file named `.env.production` in your project root:

```env
REACT_APP_API_URL=https://yourdomain.com/backend/api
REACT_APP_SITE_URL=https://yourdomain.com
```

**Replace `yourdomain.com` with your actual domain name** (e.g., `hamrodigicart.com`)

### 1.2 Build the React App

Run the build command:
```bash
npm run build
```

This creates an optimized `build` folder with all production files.

**Wait for:** "Compiled successfully!" message

---

## 🗄️ Step 2: Set Up Database in cPanel

### 2.1 Create Database

1. Login to **cPanel**
2. Find **"MySQL Databases"** or **"MySQL Database Wizard"**
3. Create a new database:
   - Database name: `yourname_hamrodigicart` (cPanel adds your username prefix)
   - Click **"Create Database"**

### 2.2 Create Database User

1. Scroll to **"MySQL Users"** section
2. Create a new user:
   - Username: `yourname_dbuser`
   - Password: **Create a strong password** (save it!)
   - Click **"Create User"**

### 2.3 Add User to Database

1. Scroll to **"Add User to Database"**
2. Select the user and database you just created
3. Click **"Add"**
4. **Grant ALL PRIVILEGES**
5. Click **"Make Changes"**

### 2.4 Import Database Schema

1. Go to **"phpMyAdmin"** in cPanel
2. Select your database from the left sidebar
3. Click **"Import"** tab
4. Click **"Choose File"** and select `backend/database/schema.sql`
5. Click **"Go"** to import

**📝 Save these credentials:**
- Database Name: `_________________`
- Database User: `_________________`
- Database Password: `_________________`
- Database Host: Usually `localhost` (check in cPanel)

---

## 📁 Step 3: Upload Files to cPanel

### 3.1 Upload Backend Files

1. Open **cPanel File Manager** (or use FTP)
2. Navigate to `public_html/` folder
3. Create a folder named `backend` (if it doesn't exist)
4. Upload **ALL files** from your local `backend/` folder to `public_html/backend/`

**Important folders to upload:**
- `backend/api/`
- `backend/admin/`
- `backend/config/`
- `backend/database/`
- `backend/uploads/` (create this folder if needed)
- All PHP files in `backend/`

### 3.2 Upload Frontend Files (from `build` folder)

1. Upload **ALL contents** of your local `build/` folder to `public_html/`

**Files to upload:**
- `index.html`
- `static/` folder (contains CSS, JS, media)
- `manifest.json`
- `robots.txt`
- `sitemap.xml`
- `.htaccess` (from `public/.htaccess`)

### 3.3 Set File Permissions

Set these permissions via File Manager (right-click → Change Permissions):
- Folders: `755`
- Files: `644`
- `backend/uploads/` folder: `755` (must be writable)

---

## ⚙️ Step 4: Configure Backend

### 4.1 Update Database Configuration

1. In cPanel File Manager, navigate to `public_html/backend/config/`
2. Edit `database.php` file:

```php
<?php
define('DB_HOST', 'localhost');  // Usually 'localhost' in cPanel
define('DB_NAME', 'yourname_hamrodigicart');  // Your database name
define('DB_USER', 'yourname_dbuser');  // Your database user
define('DB_PASS', 'your_password_here');  // Your database password
define('DB_CHARSET', 'utf8mb4');
?>
```

**Replace with your actual database credentials from Step 2**

### 4.2 Verify API Configuration

Check `backend/config/config.php` and ensure:
- `API_BASE_URL` is set correctly
- CORS is configured to allow your domain

---

## 🌐 Step 5: Configure Domain & DNS

### 5.1 Domain Configuration in cPanel

1. In cPanel, go to **"Domains"** or **"Addon Domains"**
2. Add your domain if not already added
3. Point it to `public_html/`

### 5.2 DNS Configuration

If using external DNS (like Cloudflare or domain registrar):
- **A Record**: Point `@` to your server IP
- **A Record**: Point `www` to your server IP
- **CNAME**: `www` → `yourdomain.com` (optional)

Your hosting provider can give you the correct IP address.

---

## 🔒 Step 6: Set Up SSL Certificate (HTTPS)

**SSL is REQUIRED for production!** It encrypts data and builds user trust.

### 6.1 Free SSL with cPanel (Let's Encrypt)

1. In cPanel, find **"SSL/TLS Status"** or **"Let's Encrypt SSL"**
2. Select your domain
3. Click **"Run AutoSSL"** or **"Install"**
4. Wait for SSL to be installed (usually automatic)

### 6.2 Force HTTPS

After SSL is installed, edit `public_html/.htaccess` and uncomment these lines:

```apache
# Force HTTPS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### 6.3 Update Environment Variables

Update `.env.production` to use `https://`:
```env
REACT_APP_API_URL=https://yourdomain.com/backend/api
REACT_APP_SITE_URL=https://yourdomain.com
```

Then rebuild:
```bash
npm run build
```

And re-upload the `build` folder contents.

---

## ✅ Step 7: Test Your Live Website

### 7.1 Frontend Test

Visit: `https://yourdomain.com`

Check:
- ✅ Homepage loads correctly
- ✅ Navigation works
- ✅ Products display
- ✅ Search works
- ✅ All pages accessible

### 7.2 Admin Panel Test

Visit: `https://yourdomain.com/backend/admin/login.php`

Login with default credentials:
- Username: `hamrodigicart1`
- Password: `admin123`

**⚠️ IMPORTANT:** Change password immediately after first login!

### 7.3 API Test

Visit: `https://yourdomain.com/backend/api/products`

You should see JSON data (products list).

### 7.4 File Upload Test

1. Login to admin panel
2. Go to **Products** → **Add New Product**
3. Upload an image
4. Verify image displays correctly

---

## 🔧 Step 8: Final Configuration

### 8.1 Update Admin Password

1. Login to admin panel
2. Go to **Settings** → **Change Password**
3. Set a strong password

### 8.2 Configure SMTP Email (Optional)

1. Go to **Settings** → **Email Settings**
2. Enter your SMTP credentials:
   - Gmail: Use App Password (not regular password)
   - Other providers: Use their SMTP settings
3. Test email sending

### 8.3 Update Website Settings

1. Go to **Website Settings**
2. Update:
   - Website name
   - Logo
   - Favicon
   - Contact information
   - Social media links

### 8.4 Generate Sitemap

1. Visit: `https://yourdomain.com/backend/generate-sitemap.php`
2. This creates/updates `sitemap.xml`
3. Submit to Google Search Console

---

## 📊 Step 9: Post-Deployment Checklist

- [ ] Website loads on `https://yourdomain.com`
- [ ] Admin panel accessible at `https://yourdomain.com/backend/admin/`
- [ ] All products display correctly
- [ ] Image uploads work
- [ ] Orders can be placed
- [ ] Payment screenshots can be uploaded
- [ ] Email notifications work (if configured)
- [ ] SSL certificate active (green padlock in browser)
- [ ] Mobile responsive design works
- [ ] Search functionality works
- [ ] All pages accessible (About Us, Contact Us, etc.)

---

## 🚨 Troubleshooting

### Website Shows "404 Not Found"

**Solution:**
- Check `.htaccess` file is uploaded to `public_html/`
- Verify React Router rewrite rules are present
- Check file permissions (should be 644 for files, 755 for folders)

### Images Not Loading

**Solution:**
- Verify `backend/uploads/` folder exists and is writable (755 permissions)
- Check image paths in database use `/backend/uploads/` format
- Ensure `.htaccess` allows access to uploads folder

### API Not Working / CORS Errors

**Solution:**
- Check `backend/config/config.php` CORS settings
- Verify API URL in `src/config/api.js` matches your domain
- Check browser console for specific error messages

### Database Connection Error

**Solution:**
- Verify database credentials in `backend/config/database.php`
- Check database host (might need full hostname instead of `localhost`)
- Ensure database user has correct permissions

### Admin Login Not Working

**Solution:**
- Check database connection
- Verify admin user exists in database
- Try resetting password using `backend/reset-admin-password.php`

---

## 🌟 Alternative Hosting Options

### Option 1: Vercel (Frontend) + cPanel (Backend)

1. **Frontend on Vercel:**
   - Install Vercel CLI: `npm i -g vercel`
   - Run: `vercel` in project folder
   - Follow prompts

2. **Backend on cPanel:**
   - Follow backend deployment steps above
   - Update API URL in Vercel environment variables

### Option 2: Netlify (Frontend) + cPanel (Backend)

1. **Frontend on Netlify:**
   - Connect GitHub repository
   - Build command: `npm run build`
   - Publish directory: `build`

2. **Backend on cPanel:**
   - Same as above

### Option 3: Full Cloud Hosting

**DigitalOcean / AWS / Google Cloud:**
- Requires server setup knowledge
- More control but more complex
- Better for scaling

---

## 📱 Share Your Website

Once deployed, share these URLs:

- **Website**: `https://yourdomain.com`
- **Admin Panel**: `https://yourdomain.com/backend/admin/`

Your website is now **LIVE** and accessible to everyone! 🌍

---

## 🔐 Security Reminders

1. ✅ Change default admin password immediately
2. ✅ Use strong database passwords
3. ✅ Keep PHP and MySQL updated
4. ✅ Enable SSL/HTTPS
5. ✅ Regular backups of database and files
6. ✅ Monitor for suspicious activity

---

## 📞 Need Help?

If you encounter issues:
1. Check the troubleshooting section above
2. Check cPanel error logs
3. Check browser console for frontend errors
4. Verify all file paths are correct
5. Test API endpoints directly

**Good luck with your deployment! 🚀**








