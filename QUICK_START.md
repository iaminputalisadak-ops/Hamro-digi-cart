# 🚀 Quick Start Guide

## Automated Setup (Recommended)

### Windows:
```bash
setup.bat
```

### Linux/Mac:
```bash
chmod +x setup.sh
./setup.sh
```

This will:
- ✅ Set up the database
- ✅ Install React dependencies
- ✅ Create configuration files
- ✅ Set up default admin user

## Manual Setup (If automated fails)

### 1. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE hamrodigicart;
EXIT;

# Import schema
mysql -u root -p hamrodigicart < backend/database/schema.sql
```

### 2. Configure Database
Edit `backend/config/database.php`:
```php
define('DB_USER', 'root');        // Your MySQL username
define('DB_PASS', 'your_password'); // Your MySQL password
```

### 3. Run Setup Script
```bash
cd backend
php setup.php
```

### 4. Install Dependencies
```bash
npm install
```

### 5. Create .env File
Create `.env` in root directory:
```
REACT_APP_API_URL=http://localhost:8000/api
```

## Starting the Application

### Option 1: Use Startup Scripts

**Windows:**
```bash
# Terminal 1 - Backend
start-backend.bat

# Terminal 2 - Frontend
npm start
```

**Linux/Mac:**
```bash
# Terminal 1 - Backend
chmod +x start-backend.sh
./start-backend.sh

# Terminal 2 - Frontend
npm start
```

### Option 2: Manual Start

**Terminal 1 - Start Backend:**
```bash
cd backend
php -S localhost:8000
```

**Terminal 2 - Start Frontend:**
```bash
npm start
```

## Access Points

- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:8000/api
- **Admin Panel:** http://localhost:8000/admin/login.php

## Default Login

- **Username:** `admin`
- **Password:** `admin123`

⚠️ **Change password immediately after first login!**

## Troubleshooting

### Database Connection Error
- Check MySQL is running
- Verify credentials in `backend/config/database.php`
- Ensure database exists

### Port Already in Use
- Change port in `start-backend.bat`/`start-backend.sh`
- Update `.env` file with new port

### API Not Working
- Check backend server is running
- Verify API URL in `.env` or `src/config/api.js`
- Check browser console for errors

## Next Steps

1. ✅ Run setup script
2. ✅ Start backend server
3. ✅ Start frontend
4. ✅ Login to admin panel
5. ✅ Add your first product!

🎉 You're all set!





