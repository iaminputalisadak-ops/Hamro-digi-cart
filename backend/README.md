# Hamro Digi Cart - PHP Backend

Complete PHP backend with MySQL database and admin panel for managing the Hamro Digi Cart website.

## Features

- ✅ RESTful API endpoints for Products, Categories, Orders, and Pages
- ✅ Complete Admin Panel with authentication
- ✅ MySQL database with proper schema
- ✅ File upload support for images
- ✅ Secure password hashing
- ✅ Session-based authentication

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled
- PHP extensions: PDO, PDO_MySQL, mbstring

## Installation

### 1. Database Setup

1. Create a MySQL database:
```sql
CREATE DATABASE hamrodigicart;
```

2. Import the database schema:
```bash
mysql -u root -p hamrodigicart < database/schema.sql
```

Or manually run the SQL file in your MySQL client.

### 2. Configure Database Connection

Edit `config/database.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'hamrodigicart');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

### 3. Set Up Web Server

#### Option A: Using Apache

1. Place the `backend` folder in your web root (e.g., `htdocs`, `www`, or `public_html`)
2. Ensure `.htaccess` file is in place
3. Access the admin panel at: `http://localhost/backend/admin/login.php`

#### Option B: Using PHP Built-in Server (Development)

```bash
cd backend
php -S localhost:8000
```

Then access: `http://localhost:8000/admin/login.php`

### 4. Create Uploads Directory

```bash
mkdir backend/uploads
chmod 755 backend/uploads
```

## Default Admin Credentials

- **Username:** `admin`
- **Password:** `admin123`

**⚠️ IMPORTANT:** Change the default password immediately after first login!

## API Endpoints

### Products
- `GET /api/products.php` - Get all products
- `GET /api/products.php?id={id}` - Get single product
- `POST /api/products.php` - Create product (requires auth)
- `PUT /api/products.php` - Update product (requires auth)
- `DELETE /api/products.php?id={id}` - Delete product (requires auth)

### Categories
- `GET /api/categories.php` - Get all categories
- `GET /api/categories.php?id={id}` - Get single category
- `POST /api/categories.php` - Create category (requires auth)
- `PUT /api/categories.php` - Update category (requires auth)
- `DELETE /api/categories.php?id={id}` - Delete category (requires auth)

### Orders
- `GET /api/orders.php` - Get all orders
- `GET /api/orders.php?id={id}` - Get single order
- `GET /api/orders.php?status={status}` - Get orders by status
- `POST /api/orders.php` - Create order
- `PUT /api/orders.php` - Update order (requires auth)
- `DELETE /api/orders.php?id={id}` - Delete order (requires auth)

### Pages
- `GET /api/pages.php` - Get all pages
- `GET /api/pages.php?id={id}` - Get page by ID
- `GET /api/pages.php?key={key}` - Get page by key
- `POST /api/pages.php` - Create page (requires auth)
- `PUT /api/pages.php` - Update page (requires auth)
- `DELETE /api/pages.php?id={id}` - Delete page (requires auth)

### Authentication
- `POST /api/auth.php?action=login` - Login
- `POST /api/auth.php?action=logout` - Logout
- `POST /api/auth.php?action=check` - Check login status

### File Upload
- `POST /api/upload.php` - Upload file (requires auth)

## Admin Panel

Access the admin panel at: `/admin/login.php`

### Features:
- **Dashboard:** Overview statistics
- **Products:** Full CRUD operations
- **Categories:** Manage product categories
- **Orders:** View and manage customer orders
- **Pages:** Edit static pages (About Us, Privacy Policy, etc.)
- **Settings:** Change admin password

## Database Schema

### Tables:
- `admins` - Admin users
- `categories` - Product categories
- `products` - Products
- `orders` - Customer orders
- `pages` - Static pages

## Security Notes

1. Change default admin password immediately
2. Use HTTPS in production
3. Keep PHP and MySQL updated
4. Regularly backup the database
5. Restrict file upload types and sizes
6. Use strong passwords for database

## Connecting React Frontend

Update your React frontend to use the PHP API:

1. Update `src/utils/productService.js` to fetch from PHP API instead of localStorage
2. Change API base URL to your PHP backend URL
3. Update all API calls to use the new endpoints

Example:
```javascript
const API_BASE_URL = 'http://localhost/backend/api';

export const fetchAllProducts = async () => {
  const response = await fetch(`${API_BASE_URL}/products.php`);
  const data = await response.json();
  return data.success ? data.data : [];
};
```

## Troubleshooting

### Database Connection Error
- Check database credentials in `config/database.php`
- Ensure MySQL service is running
- Verify database exists

### Permission Denied
- Check file permissions on `uploads/` directory
- Ensure PHP has write access

### API Returns 401 Unauthorized
- Ensure you're logged in to admin panel
- Check session is active
- Verify authentication headers

## Support

For issues or questions, check the code comments or database schema for details.

## License

This backend is part of the Hamro Digi Cart project.





