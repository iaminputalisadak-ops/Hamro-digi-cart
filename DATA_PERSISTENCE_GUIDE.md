# Data Persistence Guide - Hamro Digi Cart

## ✅ Yes, ALL Data is Saved in MySQL Database

All your data is **permanently stored** in the MySQL database and will **persist** even after:
- Server restarts
- Application restarts
- Browser refreshes
- System shutdowns

---

## 📊 Complete Data Storage Overview

### 1. **Products Table** - All Product Details
**Stores:**
- ✅ Product ID (auto-generated)
- ✅ Title
- ✅ Description (full text)
- ✅ Price
- ✅ Discount percentage
- ✅ Category ID (link to category)
- ✅ Product Image URL
- ✅ **Product Download Link** (for automatic email delivery)
- ✅ Status (active/inactive)
- ✅ Created timestamp
- ✅ Updated timestamp

**Persistence:** ✅ Saved permanently

---

### 2. **Orders Table** - Complete Order History
**Stores:**
- ✅ Order ID (auto-generated)
- ✅ Product ID (which product was purchased)
- ✅ Customer Name
- ✅ Customer Email
- ✅ Customer Phone Number
- ✅ Total Amount Paid
- ✅ **Payment Screenshot** (stored as file/URL)
- ✅ **Product Download Link** (for this specific order)
- ✅ Order Status (pending/approved/rejected/completed)
- ✅ Admin Notes
- ✅ **Created Timestamp** (when order was placed)
- ✅ **Updated Timestamp** (when order was modified)

**Persistence:** ✅ Full history saved permanently - **NOTHING is deleted automatically**

**Order History Includes:**
- Every order ever placed
- All payment proofs/screenshots
- Status changes (pending → approved/rejected)
- All customer details
- Complete transaction history

---

### 3. **Categories Table** - All Categories
**Stores:**
- ✅ Category ID
- ✅ Category Name
- ✅ Category Slug (URL-friendly)
- ✅ Description
- ✅ Created/Updated timestamps

**Persistence:** ✅ Saved permanently

---

### 4. **Admins Table** - Admin Users
**Stores:**
- ✅ Admin ID
- ✅ Username
- ✅ Password (hashed securely)
- ✅ Email
- ✅ Created/Updated timestamps

**Persistence:** ✅ Saved permanently

---

### 5. **Pages Table** - Website Pages
**Stores:**
- ✅ Page ID
- ✅ Page Key (unique identifier)
- ✅ Title
- ✅ Full Content (HTML/text)
- ✅ Route (URL path)
- ✅ Created/Updated timestamps

**Persistence:** ✅ Saved permanently

---

### 6. **Settings Table** - Website Settings
**Stores:**
- ✅ All website configuration
- ✅ SMTP email settings
- ✅ Product card settings
- ✅ Other system settings

**Persistence:** ✅ Saved permanently

---

### 7. **Offers Table** - Promotional Offers
**Stores:**
- ✅ Offer ID
- ✅ Title
- ✅ Description
- ✅ Discount type & value
- ✅ Start/End dates
- ✅ Image
- ✅ Link
- ✅ Status

**Persistence:** ✅ Saved permanently

---

## 💾 Database Configuration

**Database:** MySQL (`digicart`)
**Engine:** InnoDB (ACID-compliant, ensures data integrity)
**Location:** Local MySQL server (persistent storage on disk)
**Host:** localhost
**Charset:** utf8mb4 (supports all characters including emojis)

---

## 🔒 Data Safety Features

1. **Permanent Storage:** Data is stored on disk, not in memory
2. **Transaction Support:** Uses InnoDB engine for safe transactions
3. **Foreign Keys:** Products linked to categories (prevents orphaned data)
4. **Timestamps:** Every record has created_at and updated_at
5. **No Auto-Delete:** Orders are never automatically deleted (full history preserved)

---

## 📋 What Happens When:

### ✅ **Product is Added/Edited:**
- Immediately saved to `products` table
- Visible on website immediately
- Persists after server restart

### ✅ **Order is Placed:**
- Saved to `orders` table with status "pending"
- Payment screenshot saved (as file or URL)
- Full customer details saved
- **Permanent record** - never deleted

### ✅ **Order is Approved:**
- Status updated in `orders` table
- Product link automatically sent via email
- **History preserved** - can see when it was approved

### ✅ **Order is Rejected:**
- Status updated to "rejected"
- **History preserved** - original order still exists

### ✅ **Server Restarts:**
- ✅ All data remains intact
- ✅ Products still visible
- ✅ All order history preserved
- ✅ Nothing is lost

---

## 🗄️ Database Backup

**Current Setup:**
- Database: `digicart`
- Location: MySQL data directory (typically `C:\xampp\mysql\data\digicart\` on Windows with XAMPP)
- Tables stored as `.ibd` files (InnoDB format)

**To Backup Your Data:**
```bash
# Export entire database
mysqldump -u root -p digicart > backup.sql

# Import backup
mysql -u root -p digicart < backup.sql
```

---

## ✅ Summary

**YES - Everything is saved:**
- ✅ All 50 products (or more) you added
- ✅ Complete order history (every order ever placed)
- ✅ All customer information
- ✅ All payment screenshots
- ✅ All product links
- ✅ All categories
- ✅ All settings

**Data Persistence:**
- ✅ Survives server restarts
- ✅ Survives application restarts
- ✅ Permanent storage on disk
- ✅ No data loss

**Your data is SAFE and PERMANENT!** 🎉




