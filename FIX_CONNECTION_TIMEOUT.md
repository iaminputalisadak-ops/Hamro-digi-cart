# Fix: Connection Timed Out (Error 110)

## 🚨 Problem
You're seeing: **"Connection timed out (110)"** when trying to send emails.

This means your hosting provider is **blocking port 587** (TLS) or has firewall restrictions.

## ✅ Quick Fix (2 Options)

### Option 1: Switch to SSL (Port 465) - RECOMMENDED

1. Go to: **Admin Panel → Settings → SMTP Email Settings**

2. Change these settings:
   - **SMTP Port:** Change from `587` to `465`
   - **SMTP Encryption:** Change from `TLS` to `SSL`

3. Click **"💾 Save SMTP Settings"**

4. Click **"🧪 Test Email"** to verify it works

**This usually fixes the issue immediately!**

---

### Option 2: Automatic Fallback (Already Active)

The system will **automatically try SSL (port 465)** if TLS fails. However, you may still see the error if both ports are blocked.

If automatic fallback works, your settings will be updated automatically.

---

## 🔍 Why This Happens

- **Port 587 (TLS)** is commonly blocked by shared hosting providers
- **Port 465 (SSL)** is less commonly blocked
- Some hosting providers block all outbound SMTP connections for security

## 📋 Step-by-Step Fix

### Step 1: Update SMTP Settings

1. Login to Admin Panel
2. Go to **Settings** → **SMTP Email Settings**
3. Find **"SMTP Port"** field
4. Change value from `587` to `465`
5. Find **"SMTP Encryption"** dropdown
6. Change from `TLS` to `SSL`
7. Click **"💾 Save SMTP Settings"**

### Step 2: Test

1. Click **"🧪 Test Email"** button
2. Check if email is sent successfully
3. If successful, you're done! ✅

### Step 3: If Still Fails

If both TLS (587) and SSL (465) fail, your hosting provider is blocking all SMTP ports. You have these options:

#### Option A: Contact Hosting Provider
- Ask them to unblock ports 587 and 465
- Request SMTP relay access
- Ask for their recommended SMTP settings

#### Option B: Use Third-Party SMTP Service
Use a service that works through HTTP/HTTPS instead of direct SMTP:

**SendGrid (Recommended)**
- Free: 100 emails/day
- Sign up: https://sendgrid.com
- SMTP Host: `smtp.sendgrid.net`
- Port: `587` or `465`
- Encryption: `TLS` or `SSL`
- Username: `apikey`
- Password: Your SendGrid API key

**Mailgun**
- Free: 5,000 emails/month
- Sign up: https://mailgun.com
- Similar setup to SendGrid

---

## 🎯 Your Current Settings

Based on your configuration:
- ✅ Host: `smtp.gmail.com` (Correct)
- ❌ Port: `587` (May be blocked) → **Change to `465`**
- ❌ Encryption: `TLS` (May be blocked) → **Change to `SSL`**
- ✅ Email: `iaminputalisadak@gmail.com` (Correct)
- ✅ Password: App Password (Correct)

## 🔧 Quick Test

After changing to SSL/465, test immediately:

1. Click **"🧪 Test Email"** in admin panel
2. If you see "Email sent successfully!" → **Fixed!** ✅
3. If you still see timeout → Contact hosting provider or use SendGrid

---

## 💡 Pro Tip

The system now has **automatic fallback** - if TLS fails, it will automatically try SSL. But manually switching to SSL is faster and more reliable.

---

## 📞 Still Need Help?

1. Check the exact error message
2. Try the diagnostic tool: `backend/test-smtp-diagnostic.php`
3. Contact your hosting provider with:
   - Error: "Connection timed out (110)"
   - Request: Unblock SMTP ports 587 and 465
   - Or request: SMTP relay access

---

**Most likely solution: Just switch to SSL (port 465) and it will work!** 🎉








