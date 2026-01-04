# Internet Access Setup Guide

This guide will help you make your website accessible from the internet (different networks).

## Method 1: Using Ngrok (Easiest - Recommended for Testing)

Ngrok creates a secure tunnel to your local server, making it accessible from anywhere on the internet.

### Step 1: Install Ngrok
1. Download ngrok from: https://ngrok.com/download
2. Extract ngrok.exe to a folder (e.g., `C:\ngrok\`)
3. Or use npm to install globally:
   ```bash
   npm install -g ngrok
   ```

### Step 2: Start Your Servers
1. Start Backend: `start-backend.bat` (or `php -S 0.0.0.0:8000` in backend folder)
2. Start Frontend: `npm start` (running on port 3000)

### Step 3: Create Tunnels with Ngrok

**Terminal 1 - Frontend Tunnel:**
```bash
ngrok http 3000
```
This will give you a public URL like: `https://abc123.ngrok.io` → `http://localhost:3000`

**Terminal 2 - Backend Tunnel:**
```bash
ngrok http 8000
```
This will give you a public URL like: `https://xyz789.ngrok.io` → `http://localhost:8000`

### Step 4: Update API URL (If Needed)
If your frontend uses API calls, you may need to update the API base URL in your React app to use the ngrok backend URL.

### Step 5: Share URLs
Share the ngrok URLs with anyone - they can access your website from anywhere!

**Note**: Free ngrok URLs change each time you restart. Paid plans give you fixed URLs.

---

## Method 2: Port Forwarding (Permanent Solution)

This makes your website permanently accessible via your public IP address.

### Step 1: Find Your Public IP
Visit: https://whatismyipaddress.com/ or https://api.ipify.org

Your public IP will be shown (e.g., 203.0.113.45)

### Step 2: Configure Router Port Forwarding

1. **Access Router Admin Panel:**
   - Open browser and go to: `http://192.168.1.1` (or your router's IP)
   - Login with admin credentials (check router manual)

2. **Set Up Port Forwarding:**
   - Go to "Port Forwarding" or "Virtual Server" section
   - Add these rules:
   
   | Service Name | External Port | Internal IP | Internal Port | Protocol |
   |-------------|---------------|-------------|---------------|----------|
   | Frontend    | 3000          | 192.168.1.67| 3000          | TCP      |
   | Backend     | 8000          | 192.168.1.67| 8000          | TCP      |

3. **Save Settings** and restart router if needed

### Step 3: Configure Firewall

**Windows Firewall:**
1. Open Windows Defender Firewall
2. Click "Advanced Settings"
3. Click "Inbound Rules" → "New Rule"
4. Allow ports 3000 and 8000 for both TCP protocols

Or run these commands in PowerShell (as Administrator):
```powershell
netsh advfirewall firewall add rule name="Frontend Port 3000" dir=in action=allow protocol=TCP localport=3000
netsh advfirewall firewall add rule name="Backend Port 8000" dir=in action=allow protocol=TCP localport=8000
```

### Step 4: Access from Internet

Use your public IP address:
- **Your Public IP**: `27.34.64.39` (this is your current public IP)
- **Frontend**: `http://27.34.64.39:3000`
- **Backend Admin**: `http://27.34.64.39:8000/admin/login.php`

**Note**: Your public IP may change if you restart your router. For a permanent solution, consider using a dynamic DNS service.

---

## Method 3: Using Cloudflare Tunnel (Free & Professional)

Cloudflare Tunnel is a free, secure way to expose your local server.

1. Sign up at: https://cloudflare.com
2. Install cloudflared:
   ```bash
   # Windows: Download from https://github.com/cloudflare/cloudflared/releases
   ```
3. Run tunnel:
   ```bash
   cloudflared tunnel --url http://localhost:3000
   cloudflared tunnel --url http://localhost:8000
   ```

---

## ⚠️ Security Warnings

1. **Admin Panel Exposure**: Your admin panel will be accessible from the internet
   - Use a STRONG password
   - Consider restricting admin access to specific IPs
   - Use HTTPS in production

2. **Database Security**: Make sure your database is not exposed directly

3. **Rate Limiting**: Consider adding rate limiting for API endpoints

4. **HTTPS**: For production, always use HTTPS (SSL certificate)

---

## 🔧 Quick Start Script (Ngrok)

Save this as `start-internet-access.bat`:

```batch
@echo off
echo ========================================
echo   Starting Internet Access with Ngrok
echo ========================================
echo.

echo [1/2] Starting Backend Server...
start "Backend" cmd /k "cd backend && php -S 0.0.0.0:8000 -c php.ini -t . router.php"

timeout /t 2 /nobreak >nul

echo [2/2] Starting Ngrok Tunnels...
echo.
echo Frontend Tunnel (Port 3000):
start "Ngrok Frontend" cmd /k "ngrok http 3000"

timeout /t 1 /nobreak >nul

echo Backend Tunnel (Port 8000):
start "Ngrok Backend" cmd /k "ngrok http 8000"

echo.
echo ========================================
echo   Setup Complete!
echo ========================================
echo.
echo Check the ngrok windows for public URLs
echo Share those URLs to access from anywhere!
echo.
pause
```

---

## 📝 Next Steps

1. **Choose a method** (Ngrok is easiest for testing)
2. **Start your servers** (frontend and backend)
3. **Set up tunnel/port forwarding**
4. **Share the public URL** with your users
5. **Test from a different network** (e.g., mobile data)

