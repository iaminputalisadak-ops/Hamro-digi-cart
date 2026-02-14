# SMTP Quick Fix Guide

## 🚨 Most Common Issues on Live Servers

### 1. **OpenSSL Not Enabled** (Most Common)
**Symptom:** Error about SSL/TLS or OpenSSL extension

**Fix:**
- Enable in `php.ini`: `extension=openssl`
- Restart PHP/web server
- **OR** Use SSL (port 465) instead of TLS (port 587)

---

### 2. **SMTP Ports Blocked by Hosting Provider**
**Symptom:** Connection timeout or "Connection refused"

**Fix:**
- Contact hosting provider to unblock ports 587/465
- **OR** Use hosting provider's SMTP server
- **OR** Use third-party SMTP service (SendGrid, Mailgun)

---

### 3. **Wrong Gmail Password**
**Symptom:** "535 Authentication failed"

**Fix:**
- ❌ Don't use regular Gmail password
- ✅ Use **App Password** (16 characters)
- Steps:
  1. Enable 2-Step Verification: https://myaccount.google.com/security
  2. Generate App Password: https://myaccount.google.com/apppasswords
  3. Use the 16-character password (remove spaces)

---

### 4. **Server IP Blocked by Email Provider**
**Symptom:** Works locally but not on live server

**Fix:**
- Check IP blacklist: https://mxtoolbox.com/blacklists.aspx
- Use different SMTP service
- Contact email provider support

---

## 🔧 Quick Diagnostic Steps

1. **Upload diagnostic tool:**
   - File: `backend/test-smtp-diagnostic.php`
   - Access via browser
   - Review all test results

2. **Test in Admin Panel:**
   - Go to: Admin → Settings → SMTP Email Settings
   - Click "🧪 Test Email"
   - Read the error message carefully

3. **Check PHP Extensions:**
   ```php
   <?php
   echo extension_loaded('openssl') ? 'OpenSSL: OK' : 'OpenSSL: MISSING';
   ?>
   ```

4. **Test Port Connection:**
   ```php
   <?php
   $socket = @fsockopen('smtp.gmail.com', 587, $errno, $errstr, 10);
   echo $socket ? 'Port 587: OPEN' : "Port 587: BLOCKED ($errstr)";
   ?>
   ```

---

## ✅ Recommended Solutions by Hosting Type

### Shared Hosting
- Use hosting provider's SMTP server (contact support)
- OR use SendGrid/Mailgun (more reliable)

### VPS/Dedicated Server
- Enable OpenSSL in php.ini
- Unblock ports 587/465 in firewall
- Use Gmail App Password or SendGrid

### cPanel Hosting
- Use cPanel's Email Accounts SMTP
- OR use SendGrid/Mailgun

---

## 📧 Alternative SMTP Services

If Gmail/Outlook don't work:

### SendGrid (Recommended)
- **Free:** 100 emails/day
- **Setup:**
  - Sign up: https://sendgrid.com
  - Get API key
  - SMTP Host: `smtp.sendgrid.net`
  - Port: `587`, Encryption: `TLS`
  - Username: `apikey`
  - Password: Your API key

### Mailgun
- **Free:** 5,000 emails/month
- Good for production

### Amazon SES
- Very cheap (pay per email)
- Requires AWS account

---

## 🐛 Still Not Working?

1. **Check exact error message** from test email
2. **Review diagnostic tool results**
3. **Check PHP error logs**
4. **Contact hosting provider** with:
   - Error message
   - SMTP host/port you're using
   - Request to check port blocking

---

## 📚 Full Documentation

See `SMTP_TROUBLESHOOTING_GUIDE.md` for detailed troubleshooting steps.








