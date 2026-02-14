# 🚀 Production Setup Checklist for hamrodigicart.com

This is a quick reference checklist specifically for deploying to **https://hamrodigicart.com**

---

## ✅ Pre-Deployment Checklist

### 1. Environment Variables Setup

Create a `.env` file in the root directory:

```env
REACT_APP_SITE_URL=https://hamrodigicart.com
REACT_APP_API_URL=https://hamrodigicart.com/api
```

### 2. Build React Application

```bash
npm run build
```

This creates the `build` folder with production-ready files.

---

## 📤 Files to Upload

### Frontend (React Build)
Upload contents of `build` folder to `public_html/`:
- index.html
- static/ (folder with CSS, JS, media)
- manifest.json
- robots.txt
- sitemap.xml
- .htaccess

### Backend (PHP)
Upload entire `backend` folder to `public_html/backend/`

---

## 🗄️ Database Configuration

Update `backend/config/database.php`:

```php
define('DB_HOST', 'localhost'); // Usually 'localhost' in cPanel
define('DB_PORT', '3306');      // Default MySQL port
define('DB_NAME', 'your_cpanel_database_name');
define('DB_USER', 'your_cpanel_database_user');
define('DB_PASS', 'your_cpanel_database_password');
```

**Note:** Database name and user format in cPanel is usually:
- Database: `cpanelusername_dbname`
- User: `cpanelusername_dbuser`

---

## 🔧 API Configuration

The API will be accessible at:
- **API Base URL**: `https://hamrodigicart.com/backend/api`
- **Admin Panel**: `https://hamrodigicart.com/backend/admin/login.php`

---

## 📄 Important Files Created/Updated

1. ✅ **robots.txt** - Updated with correct domain
2. ✅ **sitemap.xml** - Created with base URLs
3. ✅ **generate-sitemap.php** - Script to regenerate sitemap with all products
4. ✅ **.env.production** - Production environment variables template
5. ✅ **public/index.html** - Canonical URL updated

---

## 🗺️ Sitemap Generation

### Initial Sitemap
A basic `sitemap.xml` has been created in `public/sitemap.xml`.

### Dynamic Sitemap (Recommended)
After deployment, you can generate a dynamic sitemap with all products:

1. **Via Web Browser:**
   - Visit: `https://hamrodigicart.com/backend/generate-sitemap.php`
   - This will generate and display the sitemap

2. **Via Command Line (SSH):**
   ```bash
   cd public_html/backend
   php generate-sitemap.php
   ```

3. **Automated (Cron Job):**
   Set up a cron job in cPanel to run weekly:
   ```bash
   0 2 * * 0 cd /home/username/public_html/backend && php generate-sitemap.php
   ```

**Important:** After generating, the sitemap will be saved to `public_html/sitemap.xml`

---

## 🔒 Security Setup

### After Deployment:

1. **Delete these files:**
   - `backend/setup.php`
   - `backend/install.php`
   - `backend/reset-admin-password.php`
   - `backend/update-admin-username.php`
   - `backend/check-admin.php`
   - `backend/generate-sitemap.php` (or restrict access via .htaccess)

2. **Change Admin Password:**
   - Login: `https://hamrodigicart.com/backend/admin/login.php`
   - Username: `hamrodigicart1`
   - Change password immediately!

3. **Set File Permissions:**
   - Folders: `755`
   - Files: `644`
   - `backend/uploads/`: `755` or `775`

---

## ✅ Post-Deployment Testing

1. ✅ **Frontend:** `https://hamrodigicart.com`
2. ✅ **API:** `https://hamrodigicart.com/backend/api/products.php`
3. ✅ **Admin:** `https://hamrodigicart.com/backend/admin/login.php`
4. ✅ **Sitemap:** `https://hamrodigicart.com/sitemap.xml`
5. ✅ **Robots:** `https://hamrodigicart.com/robots.txt`

---

## 🌐 Google Search Console Setup

1. **Add Property:**
   - Go to: https://search.google.com/search-console
   - Add property: `https://hamrodigicart.com`

2. **Verify Ownership:**
   - Use HTML file upload or DNS verification

3. **Submit Sitemap:**
   - Submit: `https://hamrodigicart.com/sitemap.xml`

4. **Request Indexing:**
   - Request indexing for homepage
   - Request indexing for important product pages

---

## 📊 URLs Reference

| Type | URL |
|------|-----|
| **Homepage** | https://hamrodigicart.com |
| **Admin Login** | https://hamrodigicart.com/backend/admin/login.php |
| **API Base** | https://hamrodigicart.com/backend/api |
| **Sitemap** | https://hamrodigicart.com/sitemap.xml |
| **Robots.txt** | https://hamrodigicart.com/robots.txt |
| **Products** | https://hamrodigicart.com/product/{id} |
| **Categories** | https://hamrodigicart.com/{category-slug} |

---

## 🔄 Update Sitemap After Adding Products

Whenever you add new products, regenerate the sitemap:

1. Visit: `https://hamrodigicart.com/backend/generate-sitemap.php`
2. Or run via SSH: `php backend/generate-sitemap.php`
3. Submit updated sitemap to Google Search Console

---

**All configurations have been updated for: https://hamrodigicart.com** ✅












