# Hamro Digi Cart

A full-stack e-commerce platform for selling digital products with payment verification, order management, and admin panel.

## 📋 Table of Contents

- [Overview](#overview)
- [Technologies Used](#technologies-used)
- [Features](#features)
- [Project Structure](#project-structure)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [Transferring Project with Data](#transferring-project-with-data)
- [Admin Panel](#admin-panel)
- [API Endpoints](#api-endpoints)
- [Database Schema](#database-schema)
- [Troubleshooting](#troubleshooting)

## 🎯 Overview

Hamro Digi Cart is a modern e-commerce solution designed specifically for selling digital products. It features a React-based frontend for customers and a PHP-based backend with a comprehensive admin panel for managing products, orders, categories, and website settings.

### Key Capabilities

- **Product Management**: Add, edit, and manage digital products with categories
- **Order Processing**: Handle orders with payment verification via screenshot upload
- **Payment Verification**: Manual approval system for payment screenshots
- **Email Notifications**: SMTP-based email system for order confirmations
- **Content Management**: Editable pages (About Us, Privacy Policy, Terms, etc.)
- **Website Customization**: Customizable website settings, logos, and branding
- **Responsive Design**: Mobile-friendly interface for both customers and admins

## 🛠 Technologies Used

### Frontend
- **React 19.2.3** - Modern UI library
- **React Router DOM 7.11.0** - Client-side routing
- **CKEditor 5** - Rich text editor for content management
- **CSS3** - Styling and responsive design

### Backend
- **PHP 7.4+** - Server-side scripting
- **MySQL/MariaDB** - Database management
- **PDO** - Database abstraction layer
- **SMTP** - Email functionality via sockets

### Development Tools
- **Node.js & npm** - Package management
- **React Scripts** - Build tooling
- **XAMPP/WAMP** - Local development environment (optional)

## ✨ Features

### Customer Features
- Browse products by category
- Search functionality
- Product details with images and descriptions
- Secure payment process with screenshot upload
- Order tracking and download access
- Responsive mobile design

### Admin Features
- **Dashboard**: Overview of orders, products, and statistics
- **Product Management**: CRUD operations for products
- **Category Management**: Organize products into categories
- **Order Management**: View, approve, reject, and manage orders
- **Payment Verification**: Review payment screenshots and approve orders
- **Page Editor**: Edit static pages (About Us, Privacy Policy, etc.)
- **Website Settings**: Customize site name, logo, contact info, and more
- **Email Configuration**: SMTP settings for order notifications
- **Offer Management**: Create and manage special offers
- **Product Card Settings**: Customize product display options

## 📁 Project Structure

```
hamrodigicart/
├── backend/                    # PHP Backend
│   ├── admin/                  # Admin panel pages
│   │   ├── assets/            # Admin CSS and JS
│   │   ├── includes/          # Header, sidebar components
│   │   ├── categories.php     # Category management
│   │   ├── dashboard.php      # Admin dashboard
│   │   ├── login.php          # Admin login
│   │   ├── orders.php         # Order management
│   │   ├── products.php       # Product management
│   │   ├── pages.php          # Page editor
│   │   ├── settings.php       # General settings
│   │   └── website-settings.php # Website customization
│   ├── api/                   # REST API endpoints
│   │   ├── auth.php           # Authentication
│   │   ├── categories.php     # Categories API
│   │   ├── products.php       # Products API
│   │   ├── orders.php         # Orders API
│   │   ├── pages.php          # Pages API
│   │   ├── settings.php       # Settings API
│   │   ├── upload.php         # File upload
│   │   └── website-settings.php # Website settings API
│   ├── config/                # Configuration files
│   │   ├── config.php         # Application config
│   │   ├── database.php       # Database connection
│   │   └── smtp.php           # SMTP email functions
│   ├── database/              # Database files
│   │   ├── schema.sql         # Main database schema
│   │   ├── add_navigation_menu.sql
│   │   ├── add_offers_table.sql
│   │   ├── add_product_link.sql
│   │   ├── export_database.php # Export database to SQL file
│   │   └── import_database.php # Import database from SQL file
│   ├── uploads/               # Uploaded files (images, payments)
│   ├── index.php              # Backend entry point
│   ├── router.php             # PHP built-in server router
│   ├── setup.php              # Database setup script
│   ├── reset-admin-password.php # Admin password reset
│   └── seed-products.php     # Sample data seeder
│
├── src/                       # React Frontend
│   ├── components/            # Reusable components
│   │   ├── Header.js          # Site header
│   │   ├── Footer.js          # Site footer
│   │   ├── Layout.js          # Main layout wrapper
│   │   ├── Logo.js            # Logo component
│   │   ├── ProductCard.js     # Product display card
│   │   └── PageEditor.js      # Page content editor
│   ├── pages/                 # Page components
│   │   ├── Home.js            # Homepage
│   │   ├── ProductDetails.js  # Product detail page
│   │   ├── ProductPayment.js  # Payment page
│   │   ├── ProductDownload.js # Download page
│   │   ├── OrderSuccess.js    # Order success page
│   │   ├── Search.js          # Search results
│   │   ├── AboutUs.js         # About page
│   │   ├── ContactUs.js       # Contact page
│   │   ├── PrivacyPolicy.js   # Privacy policy
│   │   ├── TermsConditions.js # Terms & conditions
│   │   └── RefundPolicy.js    # Refund policy
│   ├── config/                # Configuration
│   │   └── api.js             # API base URL and helpers
│   ├── hooks/                 # Custom React hooks
│   │   └── useWebsiteSettings.js
│   ├── utils/                 # Utility functions
│   │   ├── productService.js  # Product API calls
│   │   └── websiteSettings.js # Website settings API
│   ├── assets/                # Static assets
│   │   ├── banner1.png
│   │   ├── banner2.png
│   │   └── qr_code.png
│   ├── App.js                 # Main app component
│   └── index.js               # React entry point
│
├── public/                    # Public static files
│   ├── index.html             # HTML template
│   ├── manifest.json          # PWA manifest
│   └── robots.txt             # SEO robots file
│
├── package.json               # Node.js dependencies
├── .gitignore                # Git ignore rules
├── setup.bat                 # Windows setup script
├── setup.sh                  # Linux/Mac setup script
├── start-backend.bat         # Start backend server (Windows)
├── start-backend.sh          # Start backend server (Linux/Mac)
└── README.md                 # This file
```

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **Node.js** (v14 or higher) and **npm**
- **PHP** (7.4 or higher) with extensions:
  - PDO
  - PDO_MySQL
  - OpenSSL (for SMTP)
  - GD (for image processing)
- **MySQL/MariaDB** (5.7 or higher)
- **XAMPP/WAMP** (optional, for easier local development)
- **Git** (for cloning the repository)

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/utsab8/abc.git
cd hamrodigicart
```

### 2. Install Frontend Dependencies

```bash
npm install
```

### 3. Database Setup

#### Option A: Using phpMyAdmin (Recommended)

1. Open phpMyAdmin (usually at `http://localhost/phpmyadmin`)
2. Create a new database named `digicart`
3. Import the schema file:
   - Click on the `digicart` database
   - Go to the "Import" tab
   - Choose file: `backend/database/schema.sql`
   - Click "Go"

#### Option B: Using MySQL Command Line

```bash
# Navigate to MySQL bin directory (XAMPP example)
cd C:\xampp\mysql\bin

# Import schema
mysql.exe -u root -p digicart < "path\to\backend\database\schema.sql"
```

#### Option C: Using Setup Script

```bash
cd backend
php setup.php
```

This script will:
- Test database connection
- Create missing tables
- Set up default admin user
- Create default categories and pages

### 4. Configure Database

Edit `backend/config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');        // Change if using different port
define('DB_NAME', 'digicart');
define('DB_USER', 'root');        // Your MySQL username
define('DB_PASS', '');            // Your MySQL password
```

### 5. Configure API URL (Optional)

Create a `.env` file in the root directory:

```
REACT_APP_API_URL=http://localhost:8000/api
```

If not set, it defaults to `http://localhost:8000/api`.

## ⚙️ Configuration

### Database Configuration

File: `backend/config/database.php`

```php
define('DB_HOST', 'localhost');      // Database host
define('DB_PORT', '3306');           // Database port
define('DB_NAME', 'digicart');       // Database name
define('DB_USER', 'root');           // Database username
define('DB_PASS', '');               // Database password
```

### SMTP Configuration

Configure SMTP settings through the admin panel:
1. Login to admin panel
2. Go to Settings → Email Configuration
3. Enter your SMTP details:
   - SMTP Host (e.g., `smtp.gmail.com`)
   - SMTP Port (e.g., `587` for TLS or `465` for SSL)
   - SMTP Email (your email address)
   - SMTP Password (use App Password for Gmail)
   - Encryption (TLS or SSL)

**For Gmail:**
- Enable 2-Step Verification
- Generate an App Password
- Use the App Password (not your regular password)

### Website Settings

Customize your website through the admin panel:
- Site Name
- Logo
- Contact Email
- Contact Phone
- Social Media Links
- Footer Text
- And more...

## 📦 Transferring Project with Data

All data is stored in the database, making it easy to transfer the entire project with all your data to another device.

### Exporting Database (Before Transferring)

1. **Navigate to the database directory:**
   ```bash
   cd backend/database
   ```

2. **Run the export script:**
   ```bash
   php export_database.php
   ```

   This will create a file like `database_export_2024-01-15_143022.sql` containing all your data.

3. **The export includes:**
   - All products and categories
   - All orders and customer information
   - All pages (About Us, Privacy Policy, etc.)
   - All website settings
   - All offers
   - Admin accounts

### Transferring the Project

1. **Zip the entire project folder** including:
   - All source code files
   - The exported SQL file (`database_export_*.sql`)
   - The `backend/uploads/` folder (contains product images and payment screenshots)
   - `node_modules` folder (optional, can be regenerated with `npm install`)

2. **Send the zip file** to the other device

### Importing Database (On New Device)

1. **Extract the project** to a folder

2. **Set up the project** (if not already done):
   ```bash
   npm install
   ```

3. **Configure database** in `backend/config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_PORT', '3306');
   define('DB_NAME', 'digicart');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. **Make sure MySQL/MariaDB server is running** (XAMPP, WAMP, or standalone MySQL)

5. **Import the exported database** (the script will automatically create the database if it doesn't exist):
   ```bash
   cd backend/database
   php import_database.php database_export_2024-01-15_143022.sql
   ```

   Or if you don't specify a file, it will use the most recent export:
   ```bash
   php import_database.php
   ```

   **Note:** The import script will automatically:
   - Create the database if it doesn't exist
   - Import all tables and data
   - Restore everything exactly as it was

6. **Verify the import:**
   - Check that all tables exist
   - Verify products, orders, and settings are present
   - Test admin login

### Important Notes

- **All data is stored in the database** - no localStorage or temporary files
- **Product images** are in `backend/uploads/products/` - make sure to include this folder
- **Payment screenshots** are in `backend/uploads/payments/` - include this folder if you want to keep order history
- **Database credentials** may need to be updated on the new device (in `backend/config/database.php`)
- **SMTP settings** are stored in the database and will be transferred automatically
- **MySQL/MariaDB server must be running** on the new device (XAMPP, WAMP, or standalone MySQL)
- **The database will be created automatically** by the import script - you don't need to create it manually

### Quick Transfer Checklist

- [ ] Export database using `export_database.php`
- [ ] Include `backend/uploads/` folder in zip
- [ ] Include exported SQL file in zip
- [ ] On new device: Create database
- [ ] On new device: Import database using `import_database.php`
- [ ] On new device: Update database credentials if needed
- [ ] On new device: Test admin login
- [ ] On new device: Verify products and orders are visible

## 🏃 Running the Application

### Development Mode

#### Windows

**Single command (recommended):**
```bash
npm run dev
```

This starts:
- Backend: `http://localhost:8000`
- Frontend: `http://localhost:3000`

If port **3000** is already in use, run:
```bash
npm run dev:3001
```

This starts the frontend at `http://localhost:3001`.

**Terminal 1 - Start Backend:**
```bash
start-backend.bat
```
Or manually:
```bash
cd backend
php -S localhost:8000 -c php.ini -t . router.php
```

**Terminal 2 - Start Frontend:**
```bash
npm start
```

#### Linux/Mac

**Terminal 1 - Start Backend:**
```bash
chmod +x start-backend.sh
./start-backend.sh
```
Or manually:
```bash
cd backend
php -S localhost:8000 -c php.ini -t . router.php
```

**Terminal 2 - Start Frontend:**
```bash
npm start
```

### Access Points

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000/api
- **Admin Panel**: http://localhost:8000/admin/login.php

### Production Build

```bash
# Build React app
npm run build

# The build folder contains the production-ready files
# Deploy the build folder to your web server
# Deploy the backend folder to your PHP server
```

## ⚡ Performance + SEO

### Image optimization (recommended)
- Upload **WebP** for banners/images where possible.
- To convert existing uploaded images to WebP (max width **1200px**) run:

```bash
npm run optimize:images
```

This creates `.webp` files **alongside** originals in `backend/uploads/` (it does not delete originals). After converting, update banner image URLs in admin to point to the `.webp` version for best speed.

### Sitemap + robots
- `public/sitemap.xml` is **auto-updated** whenever products are created/updated/deleted via the admin product API.
- `public/robots.txt` references `/sitemap.xml`.

## 👨‍💼 Admin Panel

### Default Login Credentials

- **Username**: `hamrodigicart1`
- **Password**: `admin123`

⚠️ **Important**: Change the default password immediately after first login!

### Reset Admin Password

If you forget your password, use the reset script:

```bash
cd backend
php reset-admin-password.php
```

### Admin Features

1. **Dashboard**: View statistics and recent orders
2. **Products**: Manage products (add, edit, delete)
3. **Categories**: Organize products into categories
4. **Orders**: View and manage customer orders
5. **Payment Verification**: Review payment screenshots
6. **Pages**: Edit static pages (About Us, Privacy Policy, etc.)
7. **Settings**: Configure general and email settings
8. **Website Settings**: Customize website appearance
9. **Offers**: Create and manage special offers

## 🔌 API Endpoints

### Products

- `GET /api/products` - Get all products
- `GET /api/products/:id` - Get product by ID
- `GET /api/products?category=:categoryId` - Get products by category
- `GET /api/products?search=:query` - Search products
- `POST /api/products` - Create product (Admin only)
- `PUT /api/products/:id` - Update product (Admin only)
- `DELETE /api/products/:id` - Delete product (Admin only)

### Categories

- `GET /api/categories` - Get all categories
- `GET /api/categories/:id` - Get category by ID
- `POST /api/categories` - Create category (Admin only)
- `PUT /api/categories/:id` - Update category (Admin only)
- `DELETE /api/categories/:id` - Delete category (Admin only)

### Orders

- `GET /api/orders` - Get all orders (Admin only)
- `GET /api/orders/:id` - Get order by ID (Admin only)
- `POST /api/orders` - Create new order
- `PUT /api/orders/:id` - Update order status (Admin only)

### Pages

- `GET /api/pages` - Get all pages
- `GET /api/pages/:key` - Get page by key
- `POST /api/pages` - Create page (Admin only)
- `PUT /api/pages/:id` - Update page (Admin only)

### Settings

- `GET /api/settings` - Get all settings
- `PUT /api/settings` - Update settings (Admin only)

### Website Settings

- `GET /api/website-settings` - Get website settings
- `PUT /api/website-settings` - Update website settings (Admin only)

### Upload

- `POST /api/upload` - Upload file (image or payment screenshot)

### Authentication

- `POST /api/auth/login` - Admin login
- `POST /api/auth/logout` - Admin logout

## 🗄️ Database Schema

### Tables

1. **admins** - Admin user accounts
   - id, username, password, email, created_at, updated_at

2. **categories** - Product categories
   - id, name, slug, description, created_at, updated_at

3. **products** - Digital products
   - id, title, description, price, discount, category_id, image, status, product_link, created_at, updated_at

4. **orders** - Customer orders
   - id, product_id, customer_name, customer_email, customer_phone, total_amount, payment_screenshot, status, notes, created_at, updated_at

5. **pages** - Static pages content
   - id, page_key, title, content, route, created_at, updated_at

6. **settings** - Application settings (key-value pairs)
   - id, setting_key, setting_value, created_at, updated_at

7. **offers** - Special offers (if enabled)
   - id, title, description, discount, start_date, end_date, created_at, updated_at

8. **navigation_menu** - Navigation menu items (if enabled)
   - id, label, url, order, created_at, updated_at

## 🐛 Troubleshooting

### Database Connection Error

**Error**: `Database connection failed: SQLSTATE[HY000] [2002] No connection could be made`

**Solutions**:
1. Ensure MySQL is running (check XAMPP/WAMP control panel)
2. Verify database credentials in `backend/config/database.php`
3. Check if the database `digicart` exists
4. Verify MySQL port (default: 3306)
5. Check firewall settings

### PHP PDO Extension Not Found

**Error**: `PDO extension not enabled`

**Solutions**:
1. Open `php.ini` file (find with: `php --ini`)
2. Find and uncomment: `extension=pdo_mysql`
3. Restart PHP server

### OpenSSL Extension Not Available

**Error**: `OpenSSL extension is not enabled`

**Solutions**:
1. Open `php.ini` file
2. Find and uncomment: `extension=openssl`
3. Restart PHP server

### Port Already in Use

**Error**: `Address already in use`

**Solutions**:
1. Change port in `start-backend.bat` or `start-backend.sh`
2. Update API URL in `src/config/api.js` or `.env` file
3. Kill the process using the port:
   ```bash
   # Windows
   netstat -ano | findstr :8000
   taskkill /PID <PID> /F
   
   # Linux/Mac
   lsof -ti:8000 | xargs kill
   ```

### Images Not Loading

**Solutions**:
1. Check `backend/uploads/` directory permissions (should be writable)
2. Verify router.php is handling `/uploads/` routes correctly
3. Check file paths in database (should be relative: `/uploads/filename.png`)

### SMTP Email Not Working

**Solutions**:
1. Verify SMTP settings in admin panel
2. For Gmail: Use App Password (not regular password)
3. Check if OpenSSL extension is enabled
4. Try different ports (587 for TLS, 465 for SSL)
5. Check firewall/antivirus blocking SMTP connections

### React App Not Connecting to Backend

**Solutions**:
1. Verify backend is running on port 8000
2. Check `src/config/api.js` or `.env` file for correct API URL
3. Check CORS settings in `backend/config/config.php`
4. Verify both servers are running

## 📝 Additional Notes

### File Uploads

- Product images: Uploaded to `backend/uploads/`
- Payment screenshots: Uploaded to `backend/uploads/` with `payment_` prefix
- Maximum file size: 5MB (configurable in `backend/config/config.php`)

### Security Considerations

- Change default admin password immediately
- Use strong passwords for database
- Enable HTTPS in production
- Regularly update dependencies
- Keep PHP and MySQL updated
- Validate and sanitize all user inputs (already implemented)

### Performance Tips

- Optimize images before uploading
- Use CDN for static assets in production
- Enable MySQL query caching
- Use production build for React app
- Enable PHP OPcache

## 👥 Contributors

- [@iaminputalisadak-ops](https://github.com/iaminputalisadak-ops) - Development, deployment, UI/admin updates

## 📄 License

This project is private and proprietary.

## 👥 Support

For issues and questions:
- Check the troubleshooting section above
- Review the code comments
- Check database and server logs

## 🔄 Version History

- **v0.1.0** - Initial release
  - Product management
  - Order processing
  - Payment verification
  - Admin panel
  - Email notifications
  - Content management

---

**Built with ❤️ for digital product sales**
