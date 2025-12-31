-- Hamro Digi Cart Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS digicart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE digicart;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    discount DECIMAL(5, 2) DEFAULT 0.00,
    category_id INT,
    image TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    customer_name VARCHAR(255),
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50),
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_screenshot TEXT,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pages Table (for About Us, Privacy Policy, etc.)
CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    route VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_page_key (page_key),
    INDEX idx_route (route)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin (username: admin, password: admin123)
-- Password is hashed using password_hash() - default: admin123
INSERT INTO admins (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@hamrodigicart.com')
ON DUPLICATE KEY UPDATE username=username;

-- Insert default categories
INSERT INTO categories (name, slug, description) VALUES
('Reels Bundle', 'reels-bundle', 'Premium reels bundle collection'),
('WhatsApp Templates', 'whatsapp-templates', 'WhatsApp status templates'),
('Digital Planner', 'digital-planner', 'Digital planning tools'),
('Social Media Pack', 'social-media-pack', 'Social media content packs'),
('Video Templates', 'video-templates', 'Video editing templates')
ON DUPLICATE KEY UPDATE name=name;

-- Insert default pages
INSERT INTO pages (page_key, title, content, route) VALUES
('about-us', 'About Us', '<h1>About Us</h1><p>Welcome to Hamro Digi Cart. We provide the best digital products in India.</p>', '/about-us'),
('privacy-policy', 'Privacy Policy', '<h1>Privacy Policy</h1><p>Your privacy is important to us.</p>', '/privacy-policy'),
('terms-conditions', 'Terms & Conditions', '<h1>Terms & Conditions</h1><p>Please read our terms and conditions.</p>', '/terms-conditions'),
('refund-policy', 'Refund Policy', '<h1>Refund Policy</h1><p>Our refund policy details.</p>', '/refund-policy'),
('contact-us', 'Contact Us', '<h1>Contact Us</h1><p>Get in touch with us.</p>', '/contact-us')
ON DUPLICATE KEY UPDATE page_key=page_key;

