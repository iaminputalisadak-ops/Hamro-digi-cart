# Complete Setup Guide - Hamro Digi Cart

This guide will help you set up both the React frontend and PHP backend.

## Prerequisites

- Node.js and npm (for React frontend)
- PHP 7.4+ (for backend)
- MySQL 5.7+ (for database)
- Apache web server (or PHP built-in server for development)

## Step 1: Database Setup

1. **Create MySQL Database:**
   ```bash
   mysql -u root -p
   ```
   
   Then in MySQL:
   ```sql
   CREATE DATABASE hamrodigicart;
   EXIT;
   ```

2. **Import Database Schema:**
   ```bash
   mysql -u root -p hamrodigicart < backend/database/schema.sql
   ```

   Or manually:
   - Open `backend/database/schema.sql`
   - Copy and paste the SQL into your MySQL client
   - Execute it

## Step 2: Configure Backend

1. **Edit Database Configuration:**
   Open `backend/config/database.php` and update:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'hamrodigicart');
   define('DB_USER', 'root');        // Your MySQL username
   define('DB_PASS', 'your_password'); // Your MySQL password
   ```

2. **Create Uploads Directory:**
   ```bash
   mkdir backend/uploads
   chmod 755 backend/uploads
   ```

## Step 3: Set Up Backend Server

### Option A: Using Apache (Production/Development)

1. Place the `backend` folder in your web root:
   - Windows (XAMPP): `C:\xampp\htdocs\backend`
   - Linux: `/var/www/html/backend`
   - macOS (MAMP): `/Applications/MAMP/htdocs/backend`

2. Access admin panel:
   ```
   http://localhost/backend/admin/login.php
   ```

### Option B: Using PHP Built-in Server (Development Only)

```bash
cd backend
php -S localhost:8000
```

Then access:
```
http://localhost:8000/admin/login.php
```

## Step 4: Configure React Frontend

1. **Install Dependencies:**
   ```bash
   npm install
   ```

2. **Configure API URL:**
   
   Create a `.env` file in the root directory:
   ```env
   REACT_APP_API_URL=http://localhost/backend/api
   ```
   
   Or for PHP built-in server:
   ```env
   REACT_APP_API_URL=http://localhost:8000/api
   ```
   
   **Note:** If you don't create `.env`, it defaults to `http://localhost/backend/api`

3. **Start React Development Server:**
   ```bash
   npm start
   ```

   The app will open at `http://localhost:3000`

## Step 5: Access Admin Panel

1. **Login Credentials:**
   - URL: `http://localhost/backend/admin/login.php` (or your configured URL)
   - Username: `admin`
   - Password: `admin123`

2. **Change Password:**
   - After first login, go to Settings
   - Change the default password immediately

## Step 6: Test the Integration

1. **Add Products via Admin Panel:**
   - Login to admin panel
   - Go to Products
   - Click "+ Add Product"
   - Fill in product details and save

2. **View Products on Frontend:**
   - Open React app at `http://localhost:3000`
   - Products should appear on the homepage

3. **Test Order Flow:**
   - Click on a product
   - Go through download → payment flow
   - Submit order with payment screenshot
   - Check order in admin panel

## Troubleshooting

### Backend Issues

**Database Connection Error:**
- Check MySQL service is running
- Verify database credentials in `backend/config/database.php`
- Ensure database exists: `SHOW DATABASES;`

**Permission Denied:**
- Check `backend/uploads/` directory permissions
- Ensure PHP has write access

**API Returns 401:**
- Make sure you're logged into admin panel
- Check session is active

### Frontend Issues

**API Calls Failing:**
- Check API URL in `src/config/api.js` or `.env`
- Verify backend server is running
- Check browser console for CORS errors
- Ensure backend URL is correct

**Products Not Loading:**
- Check browser console for errors
- Verify API endpoint is accessible: `http://localhost/backend/api/products.php`
- Check network tab in browser dev tools

**CORS Errors:**
- Backend already has CORS headers configured
- If issues persist, check `.htaccess` file exists

## Production Deployment

### Backend:
1. Update database credentials
2. Set `display_errors = 0` in PHP config
3. Use HTTPS
4. Secure `uploads/` directory
5. Change default admin password

### Frontend:
1. Build React app: `npm run build`
2. Deploy `build/` folder to web server
3. Update API URL in `.env.production`
4. Configure reverse proxy if needed

## File Structure

```
hamrodigicart/
├── backend/
│   ├── admin/          # Admin panel
│   ├── api/            # API endpoints
│   ├── config/         # Configuration files
│   ├── database/       # SQL schema
│   └── uploads/        # Uploaded files
├── src/                # React frontend
│   ├── config/         # API configuration
│   ├── utils/          # Services
│   └── ...
└── package.json
```

## Support

- Check `backend/README.md` for API documentation
- Review code comments for implementation details
- Check browser console and PHP error logs for debugging

## Next Steps

1. ✅ Database setup complete
2. ✅ Backend configured
3. ✅ Frontend connected to API
4. ✅ Admin panel accessible
5. 🎉 Start managing your digital products!





