# 🌐 How to Access Your Website

## 🚀 Quick Access

### **Main Website (Frontend)**
👉 **http://localhost:3000**

This is your main e-commerce website where customers can:
- Browse products
- Search for items
- View product details
- Place orders
- Contact you

### **Admin Panel (Backend)**
👉 **http://localhost:8000/admin/login.php**

This is where you manage your store:
- Add/edit products
- Manage categories
- View orders
- Edit pages
- Change settings

**Login Credentials:**
- Username: `admin`
- Password: `admin123`

---

## 📋 Starting Your Servers

### **Option 1: Automatic (Recommended)**

**Start Backend:**
```bash
start-backend.bat
```

**Start Frontend:**
```bash
npm start
```

### **Option 2: Manual**

**Backend (Terminal 1):**
```bash
cd backend
php -S localhost:8000 -c php.ini
```

**Frontend (Terminal 2):**
```bash
npm start
```

---

## ✅ What's Running

- ✅ **Backend Server:** http://localhost:8000
- ✅ **Frontend Server:** http://localhost:3000 (starting...)

---

## 🎯 First Steps

1. **Wait for frontend to start** (usually takes 30-60 seconds)
   - You'll see "Compiled successfully!" in the terminal
   - Browser may open automatically

2. **Open your website:**
   - Go to: http://localhost:3000
   - You should see your homepage

3. **Login to admin panel:**
   - Go to: http://localhost:8000/admin/login.php
   - Login with: admin / admin123
   - **Change your password** in Settings!

4. **Add your first product:**
   - Go to Products in admin panel
   - Click "Add New Product"
   - Fill in details and save

---

## 🔧 Troubleshooting

### Frontend not loading?
- Wait 30-60 seconds for compilation
- Check terminal for errors
- Make sure port 3000 is not in use

### Backend not working?
- Make sure backend server is running
- Check: http://localhost:8000/check-extensions.php
- Verify database connection

### Can't login to admin?
- Use: admin / admin123
- If still not working, visit: http://localhost:8000/check-admin.php

---

## 📱 Access from Other Devices

To access from your phone or other devices on the same network:

1. Find your computer's IP address:
   ```bash
   ipconfig
   ```
   Look for "IPv4 Address" (usually 192.168.x.x)

2. Access from other device:
   - Frontend: `http://YOUR_IP:3000`
   - Backend: `http://YOUR_IP:8000`

---

**Your website is ready! 🎉**





