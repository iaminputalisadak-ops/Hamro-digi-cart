# 🚀 Deploy hamrodigicart.com - Quick Start

Your domain: **hamrodigicart.com**

---

## 📝 Important Files Created

1. **DEPLOY_HAMRODIGICART.md** - Complete step-by-step guide for your domain
2. **PRODUCTION_DEPLOYMENT_GUIDE.md** - Detailed general deployment guide
3. **DEPLOYMENT_CHECKLIST.md** - Quick checklist

---

## 🎯 Quick Steps to Go Live

### 1. Build Production Files

```bash
npm run build
```

This creates optimized files in the `build/` folder.

### 2. Setup Database in cPanel

1. Login to cPanel
2. Create MySQL database
3. Create database user
4. Add user to database (ALL PRIVILEGES)
5. Import `backend/database/schema.sql` via phpMyAdmin

### 3. Upload Files

**Frontend:**
- Upload ALL contents of `build/` folder to `public_html/`

**Backend:**
- Upload ALL contents of `backend/` folder to `public_html/backend/`

**Permissions:**
- Create `public_html/backend/uploads/` folder
- Set permissions to 755

### 4. Configure Database

Edit `public_html/backend/config/database.php` with your database credentials.

### 5. Install SSL

1. Install SSL certificate in cPanel (Let's Encrypt)
2. Uncomment HTTPS redirect in `public_html/.htaccess`

### 6. Test

Visit: **https://hamrodigicart.com**

---

## 📚 Full Instructions

For complete step-by-step instructions, see:
- **DEPLOY_HAMRODIGICART.md** (recommended - specific to your domain)

For general reference:
- **PRODUCTION_DEPLOYMENT_GUIDE.md**

---

## 🔗 Your Live URLs

After deployment:
- **Website**: https://hamrodigicart.com
- **Admin Panel**: https://hamrodigicart.com/backend/admin/login.php
- **API**: https://hamrodigicart.com/backend/api/

**Default Admin Login:**
- Username: `hamrodigicart1`
- Password: `admin123` (⚠️ Change immediately!)

---

## ✅ Ready to Deploy?

1. Open **DEPLOY_HAMRODIGICART.md**
2. Follow the steps
3. Your website will be live! 🎉









