# ✅ Quick Deployment Checklist

Use this checklist to deploy your website to production.

## Pre-Deployment

- [ ] Domain name registered and ready
- [ ] Web hosting account set up (cPanel access)
- [ ] Database access available
- [ ] File upload access (FTP/cPanel File Manager)

## Build & Prepare

- [ ] Edit `.env.production` with your domain:
  ```env
  REACT_APP_API_URL=https://yourdomain.com/backend/api
  REACT_APP_SITE_URL=https://yourdomain.com
  ```
- [ ] Run `npm run build` OR run `deploy-to-production.bat`
- [ ] Verify `build/` folder created successfully
- [ ] Check all files in `build/` folder

## Database Setup (cPanel)

- [ ] Create database in cPanel
- [ ] Create database user in cPanel
- [ ] Add user to database with ALL PRIVILEGES
- [ ] Import `backend/database/schema.sql` via phpMyAdmin
- [ ] Save database credentials (name, user, password, host)

## Upload Files

- [ ] Upload ALL contents of `build/` folder to `public_html/`
- [ ] Upload ALL contents of `backend/` folder to `public_html/backend/`
- [ ] Upload `.htaccess` from `public/.htaccess` to `public_html/`
- [ ] Create `public_html/backend/uploads/` folder
- [ ] Set folder permissions: `backend/uploads/` = 755 (writable)

## Configuration

- [ ] Update `backend/config/database.php` with your database credentials
- [ ] Verify domain points to `public_html/` in cPanel
- [ ] Test: Visit `https://yourdomain.com`

## SSL Certificate (REQUIRED)

- [ ] Install SSL certificate in cPanel (Let's Encrypt)
- [ ] Uncomment HTTPS redirect in `.htaccess`
- [ ] Update `.env.production` to use `https://`
- [ ] Rebuild: `npm run build`
- [ ] Re-upload `build/` folder contents
- [ ] Verify: Green padlock in browser

## Testing

- [ ] Homepage loads: `https://yourdomain.com`
- [ ] Products display correctly
- [ ] Search works
- [ ] Admin panel accessible: `https://yourdomain.com/backend/admin/login.php`
- [ ] Can login with default credentials
- [ ] Image upload works in admin panel
- [ ] Can add/edit products
- [ ] Orders can be placed
- [ ] Payment screenshot upload works
- [ ] Mobile view works correctly

## Security & Final Steps

- [ ] Change admin password immediately
- [ ] Configure SMTP email (optional)
- [ ] Update website settings (logo, favicon, contact info)
- [ ] Generate sitemap: Visit `https://yourdomain.com/backend/generate-sitemap.php`
- [ ] Submit sitemap to Google Search Console

## Post-Deployment

- [ ] Share website URL with users
- [ ] Monitor for any errors
- [ ] Set up regular backups
- [ ] Test from different devices/networks

---

## Quick Commands

```bash
# Build for production
npm run build

# Or use the helper script
deploy-to-production.bat
```

---

## Your Website URLs

- **Website**: `https://yourdomain.com`
- **Admin**: `https://yourdomain.com/backend/admin/`
- **API**: `https://yourdomain.com/backend/api/`

---

## Need Help?

See `PRODUCTION_DEPLOYMENT_GUIDE.md` for detailed step-by-step instructions.








