-- Add homepage_banners table for Homepage Banner management
CREATE TABLE IF NOT EXISTS homepage_banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(500),
    image_path TEXT NOT NULL,
    image_path_desktop TEXT,
    image_path_mobile TEXT,
    use_device_images TINYINT(1) NOT NULL DEFAULT 0,
    link_url TEXT,
    button_text VARCHAR(100),
    button_link TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    display_order INT NOT NULL DEFAULT 0,
    start_at DATETIME NULL,
    end_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_display_order (display_order),
    INDEX idx_schedule (start_at, end_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


