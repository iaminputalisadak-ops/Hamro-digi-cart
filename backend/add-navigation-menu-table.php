<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    
    // Check if table already exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'navigation_menu_items'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS navigation_menu_items (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        echo "✓ navigation_menu_items table created!\n";
    } else {
        echo "✓ navigation_menu_items table already exists\n";
    }
    
    // Check if default items exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM navigation_menu_items");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        echo "Adding default navigation items...\n";
        $defaultItems = [
            ['WhatsApp', '/whatsapp', 1],
            ['Reels Bundle', '/reels-bundle', 2],
            ['Combo Reels bundle', '/combo-reels-bundle', 3],
            ['Instagram Reels Bundle', '/instagram-reels-bundle', 4],
            ['Reels bundle ₹99', '/reels-bundle-99', 5],
            ['Reels bundle ₹149', '/reels-bundle-149', 6],
            ['Reels bundle ₹199', '/reels-bundle-199', 7],
            ['Reels bundle', '/reels-bundle', 8],
            ['Follow Us', '/follow-us', 9]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO navigation_menu_items (label, link, order_index, status) VALUES (?, ?, ?, 'active')");
        foreach ($defaultItems as $item) {
            $stmt->execute($item);
            echo "  ✓ Added: {$item[0]}\n";
        }
    } else {
        echo "✓ Navigation items already exist ($count items)\n";
    }
    
    echo "\n✓ Navigation menu setup complete!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}




