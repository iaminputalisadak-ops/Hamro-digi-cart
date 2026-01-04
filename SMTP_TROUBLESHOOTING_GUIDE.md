# SMTP Troubleshooting Guide for Live Server

This guide helps you diagnose and fix SMTP email issues on your live server.

## 🔍 Common Issues & Solutions

### 1. **OpenSSL Extension Not Enabled**
**Problem:** PHP's OpenSSL extension is required for TLS/SSL connections.

**Check:**
- Create a PHP file with: `<?php phpinfo(); ?>` and check if `openssl` appears in the list
- Or run: `<?php echo extension_loaded('openssl') ? 'Enabled' : 'Disabled'; ?>`

**Solution:**
- Enable in `php.ini`: `extension=openssl`
- Restart PHP/web server after enabling
- If you can't modify `php.ini`, contact your hosting provider

**Alternative:** Use SSL (port 465) instead of TLS (port 587) if TLS doesn't work

---

### 2. **Firewall Blocking SMTP Ports**
**Problem:** Server firewall or hosting provider blocks outbound SMTP connections.

**Common Blocked Ports:**
- Port 587 (TLS)
- Port 465 (SSL)
- Port 25 (unencrypted - usually blocked)

**Solution:**
- Contact hosting provider to unblock SMTP ports
- Check if your hosting plan allows outbound SMTP connections
- Some shared hosting providers block SMTP for security

**Test Connection:**
```php
<?php
$host = 'smtp.gmail.com';
$port = 587;
$socket = @fsockopen($host, $port, $errno, $errstr, 10);
if ($socket) {
    echo "Port $port is open";
    fclose($socket);
} else {
    echo "Port $port is blocked: $errstr ($errno)";
}
?>
```

---

### 3. **Incorrect SMTP Credentials**

**Gmail Specific:**
- ❌ **Don't use:** Your regular Gmail password
- ✅ **Use:** App Password (16-character password)
- **Requirements:**
  1. Enable 2-Step Verification on your Google account
  2. Go to: https://myaccount.google.com/apppasswords
  3. Generate an App Password for "Mail"
  4. Use this 16-character password (remove spaces if displayed with spaces)

**Other Email Providers:**
- Verify username/email is correct
- Check if password has special characters that need escaping
- Ensure account is not locked or suspended

---

### 4. **Server IP Blocked by Email Provider**
**Problem:** Email provider (Gmail, Outlook, etc.) blocks your server's IP address.

**Signs:**
- Connection works but authentication fails
- Error: "535 Authentication failed"
- Works on localhost but not on live server

**Solution:**
- Check if your server IP is on any blacklist: https://mxtoolbox.com/blacklists.aspx
- Contact email provider support
- Use a different SMTP service (SendGrid, Mailgun, etc.)
- Consider using a dedicated SMTP service instead of Gmail/Outlook

---

### 5. **SSL/TLS Certificate Verification Issues**
**Problem:** Certificate verification fails (though your code disables this, some servers still enforce it).

**Current Code:** Already disables certificate verification:
```php
'verify_peer' => false,
'verify_peer_name' => false,
'allow_self_signed' => true
```

**If Still Failing:**
- Try different encryption method (TLS vs SSL)
- Switch ports: 587 (TLS) ↔ 465 (SSL)

---

### 6. **PHP Configuration Restrictions**

**Check These Settings:**
```php
<?php
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'On' : 'Off') . "\n";
echo "OpenSSL: " . (extension_loaded('openssl') ? 'Enabled' : 'Disabled') . "\n";
echo "Socket Functions: " . (function_exists('fsockopen') ? 'Available' : 'Disabled') . "\n";
?>
```

**Common Restrictions:**
- `allow_url_fopen` disabled (usually not needed for SMTP)
- `fsockopen()` disabled (your code uses `stream_socket_client` which is better)
- `stream_socket_client()` disabled (contact hosting provider)

---

### 7. **Timeout Issues**
**Problem:** Connection times out before completing.

**Current Timeout:** 30 seconds (in your code)

**If Timeouts Occur:**
- Increase timeout in `smtp.php` line 71: Change `30` to `60`
- Check server network speed
- Verify SMTP host is reachable from server

---

### 8. **Shared Hosting Restrictions**
**Problem:** Many shared hosting providers restrict SMTP for security.

**Common Restrictions:**
- Only allow SMTP through their own mail servers
- Block external SMTP connections
- Require using their SMTP relay

**Solution:**
- Use hosting provider's SMTP server instead of Gmail/Outlook
- Contact support for SMTP relay configuration
- Consider upgrading to VPS/dedicated server
- Use third-party SMTP service (SendGrid, Mailgun, Amazon SES)

---

### 9. **Email Provider Security Settings**

**Gmail:**
- ✅ 2-Step Verification must be enabled
- ✅ App Password must be used (not regular password)
- ✅ "Less secure app access" is deprecated (use App Passwords)

**Outlook/Office365:**
- May require app-specific password
- Check if account has security restrictions
- Verify SMTP server: `smtp.office365.com` (port 587, TLS)

**Yahoo:**
- Requires App Password
- SMTP: `smtp.mail.yahoo.com` (port 587, TLS)

---

### 10. **Database Connection Issues**
**Problem:** SMTP settings not retrieved from database.

**Check:**
- Verify database connection works
- Check if SMTP settings exist in `settings` table:
  ```sql
  SELECT * FROM settings WHERE setting_key LIKE 'smtp_%';
  ```
- Ensure settings are saved correctly in admin panel

---

## 🧪 Diagnostic Steps

### Step 1: Test PHP Extensions
Create `test-extensions.php`:
```php
<?php
echo "OpenSSL: " . (extension_loaded('openssl') ? '✅ Enabled' : '❌ Disabled') . "\n";
echo "Socket Functions: " . (function_exists('stream_socket_client') ? '✅ Available' : '❌ Disabled') . "\n";
echo "PHP Version: " . phpversion() . "\n";
?>
```

### Step 2: Test Port Connectivity
Create `test-port.php`:
```php
<?php
$host = 'smtp.gmail.com'; // Change to your SMTP host
$port = 587; // Change to your SMTP port

$socket = @stream_socket_client(
    "$host:$port",
    $errno,
    $errstr,
    10
);

if ($socket) {
    echo "✅ Port $port is accessible\n";
    fclose($socket);
} else {
    echo "❌ Port $port is blocked: $errstr ($errno)\n";
}
?>
```

### Step 3: Test SMTP Configuration
Use the built-in test feature:
- Go to: Admin Panel → Settings → SMTP Email Settings
- Click "🧪 Test Email" button
- Check the error message for specific issues

Or use: Admin Panel → Test Admin Email

### Step 4: Check Error Logs
- Check PHP error logs: Usually in `error_log` or hosting control panel
- Check web server logs
- Enable error logging in your code temporarily:
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```

### Step 5: Test with Different Settings
Try these combinations:

**Gmail:**
- Host: `smtp.gmail.com`
- Port: `587`, Encryption: `TLS`
- OR Port: `465`, Encryption: `SSL`

**Outlook:**
- Host: `smtp.office365.com`
- Port: `587`, Encryption: `TLS`

**SendGrid:**
- Host: `smtp.sendgrid.net`
- Port: `587`, Encryption: `TLS`
- Username: `apikey`
- Password: Your SendGrid API key

---

## 🔧 Quick Fixes to Try

1. **Switch Encryption Method:**
   - If using TLS (587), try SSL (465)
   - If using SSL (465), try TLS (587)

2. **Verify App Password (Gmail):**
   - Make sure you're using App Password, not regular password
   - Remove any spaces from the App Password

3. **Check SMTP Settings in Database:**
   ```sql
   SELECT setting_key, setting_value 
   FROM settings 
   WHERE setting_key LIKE 'smtp_%';
   ```

4. **Test with Simple Email:**
   - Use the test email feature in admin panel
   - Send to your own email first

5. **Contact Hosting Provider:**
   - Ask if SMTP ports are blocked
   - Ask if OpenSSL is enabled
   - Ask for recommended SMTP settings

---

## 📋 Recommended SMTP Services for Production

If Gmail/Outlook don't work, consider these:

1. **SendGrid** (Free tier: 100 emails/day)
   - Reliable and production-ready
   - Good deliverability
   - Easy setup

2. **Mailgun** (Free tier: 5,000 emails/month)
   - Developer-friendly
   - Good API

3. **Amazon SES** (Very cheap, pay per email)
   - Highly scalable
   - Requires AWS account

4. **Hosting Provider's SMTP**
   - Usually works best on shared hosting
   - Contact support for credentials

---

## 🐛 Debug Mode

To get more detailed error messages, temporarily modify `backend/config/smtp.php`:

Add at the beginning of `sendSMTPViaSocket()`:
```php
error_log("SMTP Debug - Host: $host, Port: $port, Encryption: $encryption");
error_log("SMTP Debug - Username: $username");
```

This will log detailed information to your error log.

---

## ✅ Checklist

Before contacting support, verify:

- [ ] OpenSSL extension is enabled
- [ ] SMTP ports (587/465) are not blocked
- [ ] Using correct SMTP credentials (App Password for Gmail)
- [ ] SMTP settings are saved in database
- [ ] Test email feature shows specific error message
- [ ] Tried both TLS (587) and SSL (465)
- [ ] Server IP is not blacklisted
- [ ] Email provider account is active and not locked

---

## 📞 Getting Help

If none of the above works:

1. **Check the exact error message** from test email feature
2. **Check PHP error logs** for detailed errors
3. **Contact your hosting provider** with:
   - Error message
   - SMTP host and port you're trying to use
   - Request to check if ports are blocked
4. **Consider using a dedicated SMTP service** (SendGrid, Mailgun, etc.)

---

## 🔗 Useful Links

- Gmail App Passwords: https://myaccount.google.com/apppasswords
- Test SMTP Connection: https://www.socketlabs.com/smtp-test/
- Check IP Blacklist: https://mxtoolbox.com/blacklists.aspx
- PHP OpenSSL Documentation: https://www.php.net/manual/en/book.openssl.php







