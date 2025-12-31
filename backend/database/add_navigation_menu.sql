-- Navigation Menu Items Table
CREATE TABLE IF NOT EXISTS navigation_menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(255) NOT NULL,
    link VARCHAR(500) NOT NULL,
    icon VARCHAR(100),
    order_index INT DEFAULT 0,
    is_dropdown BOOLEAN DEFAULT FALSE,
    parent_id INT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES navigation_menu_items(id) ON DELETE CASCADE,
    INDEX idx_order (order_index),
    INDEX idx_status (status),
    INDEX idx_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default navigation items
INSERT INTO navigation_menu_items (label, link, order_index, status) VALUES
('WhatsApp', '/whatsapp', 1, 'active'),
('Reels Bundle', '/reels-bundle', 2, 'active'),
('Combo Reels bundle', '/combo-reels-bundle', 3, 'active'),
('Instagram Reels Bundle', '/instagram-reels-bundle', 4, 'active'),
('Reels bundle ₹99', '/reels-bundle-99', 5, 'active'),
('Reels bundle ₹149', '/reels-bundle-149', 6, 'active'),
('Reels bundle ₹199', '/reels-bundle-199', 7, 'active'),
('Reels bundle', '/reels-bundle', 8, 'active'),
('Follow Us', '/follow-us', 9, 'active')
ON DUPLICATE KEY UPDATE label=label;




