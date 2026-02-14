# 🚀 Quick Start - Make Your Website Live

## What You Need

1. ✅ Domain name (e.g., hamrodigicart.com)
2. ✅ Web hosting with cPanel
3. ✅ Database access (MySQL)

---

## 3 Simple Steps

### Step 1: Prepare Build

1. Create `.env.production` file in project root:
   ```
   REACT_APP_API_URL=https://yourdomain.com/backend/api
   REACT_APP_SITE_URL=https://yourdomain.com
   ```
   (Replace `yourdomain.com` with your actual domain)

2. Build the app:
   ```bash
   npm run build
   ```
   OR run: `deploy-to-production.bat`

### Step 2: Upload to cPanel

1. **Login to cPanel**
2. **Upload Frontend:**
   - Go to File Manager → `public_html/`
   - Upload ALL files from `build/` folder

3. **Upload Backend:**
   - Create folder: `public_html/backend/`
   - Upload ALL files from `backend/` folder

4. **Set Permissions:**
   - Create folder: `public_html/backend/uploads/`
   - Set permissions: `755` (right-click → Change Permissions)

### Step 3: Setup Database

1. **Create Database in cPanel:**
   - MySQL Databases → Create Database
   - Create User → Add to Database
   - Grant ALL PRIVILEGES

2. **Import Database:**
   - phpMyAdmin → Select Database → Import
   - Upload: `backend/database/schema.sql`

3. **Configure:**
   - Edit: `public_html/backend/config/database.php`
   - Add your database credentials

### Step 4: Install SSL

1. In cPanel → SSL/TLS Status
2. Install Let's Encrypt SSL for your domain
3. Edit `public_html/.htaccess` - uncomment HTTPS redirect

---

## Test Your Website

Visit: `https://yourdomain.com`

**Admin Login:**
- URL: `https://yourdomain.com/backend/admin/login.php`
- Username: `hamrodigicart1`
- Password: `admin123`
- ⚠️ **Change password immediately!**

---

## That's It! 🎉

Your website is now LIVE and accessible to everyone!

---

## Full Guide

For detailed instructions, see: `PRODUCTION_DEPLOYMENT_GUIDE.md`

For checklist, see: `DEPLOYMENT_CHECKLIST.md`









