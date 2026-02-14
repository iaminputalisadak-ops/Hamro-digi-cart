# Quick Internet Access Guide 🌐

Make your website accessible from anywhere in the world!

## 🚀 Quick Start (Easiest Method - Ngrok)

### Step 1: Install Ngrok
1. Download: https://ngrok.com/download
2. Extract `ngrok.exe` to this folder OR add to system PATH

### Step 2: Run the Script
Double-click: `start-internet-access.bat`

This will:
- ✅ Start your backend server
- ✅ Start your frontend server  
- ✅ Create public URLs via ngrok

### Step 3: Get Your Public URLs
Check the **Ngrok** windows that open. You'll see URLs like:
- Frontend: `https://abc123.ngrok.io`
- Backend: `https://xyz789.ngrok.io`

**Share these URLs with anyone!** They can access your website from anywhere.

---

## 🔧 Permanent Solution (Port Forwarding)

### Your Network Info:
- **Local IP**: 192.168.1.67
- **Public IP**: 27.34.64.39 (may change after router restart)

### Step 1: Configure Router
1. Open browser → `http://192.168.1.1`
2. Login to router
3. Find "Port Forwarding" or "Virtual Server"
4. Add these rules:

| Service | External Port | Internal IP | Internal Port |
|---------|--------------|-------------|---------------|
| Frontend | 3000 | 192.168.1.67 | 3000 |
| Backend | 8000 | 192.168.1.67 | 8000 |

### Step 2: Allow Firewall
Run PowerShell **as Administrator**:
```powershell
netsh advfirewall firewall add rule name="Frontend Port 3000" dir=in action=allow protocol=TCP localport=3000
netsh advfirewall firewall add rule name="Backend Port 8000" dir=in action=allow protocol=TCP localport=8000
```

### Step 3: Access from Internet
- **Frontend**: http://27.34.64.39:3000
- **Admin**: http://27.34.64.39:8000/admin/login.php

---

## ⚠️ Important Notes

1. **Public IP Changes**: Your IP may change after router restart. Use Dynamic DNS for permanent URLs.

2. **Security**: 
   - Use strong admin passwords
   - Consider HTTPS for production
   - The admin panel will be accessible from internet

3. **Ngrok URLs**: Free ngrok URLs change each restart. For fixed URLs, use paid plan.

---

## 📱 Test It!

1. Share the ngrok URL or public IP with someone
2. Ask them to open it from their phone/computer
3. It should work from anywhere in the world!

---

**Need Help?** Check `INTERNET_ACCESS_SETUP.md` for detailed instructions.









