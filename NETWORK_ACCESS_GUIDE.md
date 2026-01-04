# Network Access Guide

Your website is now configured to be accessible from any device on your local network and from the internet.

## 🌐 Access URLs

### From Your Computer (Localhost)
- **Frontend**: http://localhost:3000
- **Backend Admin**: http://localhost:8000/admin/login.php

### From Other Devices on Same Network
- **Frontend**: http://192.168.1.67:3000 (replace with your IP)
- **Backend Admin**: http://192.168.1.67:8000/admin/login.php

### From Internet (If Port Forwarding is Configured)
- **Frontend**: http://YOUR_PUBLIC_IP:3000
- **Backend Admin**: http://YOUR_PUBLIC_IP:8000/admin/login.php

## 🔍 Find Your IP Address

### Windows:
```cmd
ipconfig
```
Look for "IPv4 Address" - Your IP is shown above (192.168.1.67)

### Linux/Mac:
```bash
ifconfig
# or
ip addr show
```

## 🚀 Starting Servers with Network Access

### Method 1: Using npm start (Frontend)
The React dev server is now configured to accept connections from any network interface.
Just run:
```bash
npm start
```

### Method 2: Using Backend Scripts
```bash
# Windows
start-backend.bat

# Linux/Mac
./start-backend.sh
```

## ⚠️ Important Notes

1. **Firewall**: Make sure your firewall allows connections on ports 3000 and 8000
2. **Same Network**: Devices must be on the same local network to access via local IP
3. **Security**: The admin panel is accessible from network - keep your password secure!
4. **Internet Access**: To access from outside your network, you need:
   - Router port forwarding (ports 3000 and 8000)
   - Or use a service like ngrok for temporary access

## 🔒 Security Recommendations

- Use strong admin passwords
- Consider using HTTPS in production
- Restrict admin panel access if needed
- Keep your servers updated

## 🛑 Stopping Network Access

To restrict to localhost only:

1. **Frontend**: Remove `HOST=0.0.0.0` from package.json start script
2. **Backend**: Change `0.0.0.0:8000` back to `localhost:8000` in start scripts








