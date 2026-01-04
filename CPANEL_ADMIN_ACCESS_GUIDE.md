# cPanel Admin Panel Access Guide

## 📍 How to Access Admin Panel Through cPanel

After deploying your application to cPanel, follow these steps to access the admin panel:

### Step 1: Determine Your Domain URL

Your admin panel URL depends on your domain name. For example:
- If your domain is: `yourdomain.com`
- Admin panel URL will be: `https://yourdomain.com/backend/admin/login.php`

### Step 2: Access Admin Panel

1. **Open your web browser** (Chrome, Firefox, Safari, etc.)

2. **Type in the address bar:**
   ```
   https://yourdomain.com/backend/admin/login.php
   ```
   Replace `yourdomain.com` with your actual domain name.

3. **Or if you have a subdomain:**
   ```
   https://www.yourdomain.com/backend/admin/login.php
   ```

### Step 3: Login Credentials

**Default Login Credentials:**
- **Username:** `hamrodigicart1`
- **Password:** `admin123`

⚠️ **IMPORTANT:** Change this password immediately after first login for security!

### Step 4: Alternative Access Methods

#### Method 1: Via cPanel File Manager
1. Log in to your cPanel
2. Go to **File Manager**
3. Navigate to `public_html/backend/admin/`
4. Right-click on `login.php`
5. Select **View** or open the file URL directly

#### Method 2: Direct URL Construction
The admin panel is located at:
```
https://yourdomain.com/backend/admin/login.php
```

Parts breakdown:
- `yourdomain.com` = Your domain name
- `/backend/` = Backend folder (uploaded to public_html)
- `/admin/` = Admin panel folder
- `login.php` = Login page

### Step 5: After Successful Login

Once logged in, you'll be redirected to:
```
https://yourdomain.com/backend/admin/dashboard.php
```

You can access different admin sections:
- Dashboard: `https://yourdomain.com/backend/admin/dashboard.php`
- Products: `https://yourdomain.com/backend/admin/products.php`
- Orders: `https://yourdomain.com/backend/admin/orders.php`
- Settings: `https://yourdomain.com/backend/admin/settings.php`
- Website Settings: `https://yourdomain.com/backend/admin/website-settings.php`
- Pages: `https://yourdomain.com/backend/admin/pages.php`
- Categories: `https://yourdomain.com/backend/admin/categories.php`
- Offers: `https://yourdomain.com/backend/admin/offers.php`

## 🔒 Security Notes

1. **Change Default Password:** Change `admin123` immediately after first login
2. **HTTPS Required:** Make sure your site has SSL certificate installed
3. **Admin URL Protection:** Consider restricting admin panel access by IP if possible
4. **Regular Updates:** Keep your application updated for security patches

## 🛠️ Troubleshooting

### If you get "404 Not Found" error:
- Check that the `backend` folder is uploaded to `public_html/`
- Verify file permissions (folders: 755, files: 644)
- Check if `.htaccess` file exists in `public_html/backend/`

### If you get "Database Connection Failed":
- Update database credentials in `backend/config/database.php`
- Verify database exists in cPanel MySQL Databases
- Check database user has proper permissions

### If login page doesn't load:
- Check PHP version in cPanel (should be 7.4 or higher)
- Verify `backend/admin/login.php` file exists
- Check error logs in cPanel

## 📝 Quick Reference

**Admin Login URL Format:**
```
https://[your-domain]/backend/admin/login.php
```

**Example URLs:**
- `https://hamrodigicart.com/backend/admin/login.php`
- `https://www.hamrodigicart.com/backend/admin/login.php`
- `https://shop.yourdomain.com/backend/admin/login.php` (if using subdomain)

**Default Credentials:**
- Username: `hamrodigicart1`
- Password: `admin123`








