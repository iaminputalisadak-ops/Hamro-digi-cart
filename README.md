# 🛒 Hamro Digi Cart

Complete e-commerce platform for selling digital products with React frontend and PHP backend.

## ✨ Features

- 🎨 Modern React frontend
- 🔧 PHP backend with MySQL database
- 👨‍💼 Complete admin panel
- 📦 Product management
- 🛒 Order processing
- 📄 Page content management
- 🔐 Secure authentication

## 🚀 Quick Start

### Automated Setup (Easiest)

**Windows:**
```bash
setup.bat
```

**Linux/Mac:**
```bash
chmod +x setup.sh
./setup.sh
```

### Manual Setup

1. **Database Setup:**
   ```bash
   mysql -u root -p < backend/database/schema.sql
   ```

2. **Configure Database:**
   Edit `backend/config/database.php` with your MySQL credentials

3. **Run Setup:**
   ```bash
   cd backend
   php setup.php
   ```

4. **Install Dependencies:**
   ```bash
   npm install
   ```

5. **Start Application:**
   ```bash
   # Terminal 1 - Backend
   cd backend
   php -S localhost:8000
   
   # Terminal 2 - Frontend
   npm start
   ```

## 📁 Project Structure

```
hamrodigicart/
├── backend/              # PHP backend
│   ├── admin/           # Admin panel
│   ├── api/             # API endpoints
│   ├── config/          # Configuration
│   ├── database/        # SQL schema
│   └── uploads/         # Uploaded files
├── src/                 # React frontend
│   ├── components/      # React components
│   ├── pages/           # Page components
│   ├── config/          # API configuration
│   └── utils/           # Utilities
└── public/              # Static files
```

## 🔗 Access Points

- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:8000/api
- **Admin Panel:** http://localhost:8000/admin/login.php

## 🔑 Default Credentials

- **Username:** `admin`
- **Password:** `admin123`

⚠️ **Change password immediately after first login!**

## 📚 Documentation

- [QUICK_START.md](QUICK_START.md) - Quick setup guide
- [SETUP_GUIDE.md](SETUP_GUIDE.md) - Detailed setup instructions
- [INTEGRATION_COMPLETE.md](INTEGRATION_COMPLETE.md) - Integration details
- [backend/README.md](backend/README.md) - Backend API documentation

## 🛠️ Technology Stack

**Frontend:**
- React 19
- React Router
- CSS3

**Backend:**
- PHP 7.4+
- MySQL 5.7+
- PDO

## 📝 Requirements

- Node.js 14+
- PHP 7.4+
- MySQL 5.7+
- npm or yarn

## 🎯 Features

### Frontend
- ✅ Product browsing
- ✅ Category filtering
- ✅ Search functionality
- ✅ Product details
- ✅ Order placement
- ✅ Payment proof upload

### Admin Panel
- ✅ Dashboard with statistics
- ✅ Product management (CRUD)
- ✅ Category management
- ✅ Order management
- ✅ Page content editor
- ✅ Settings

## 🐛 Troubleshooting

See [QUICK_START.md](QUICK_START.md) for common issues and solutions.

## 📄 License

This project is part of Hamro Digi Cart.

## 🤝 Support

For issues or questions, check the documentation files or review the code comments.

---

**Made with ❤️ for digital product sellers**





