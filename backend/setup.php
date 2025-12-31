<?php
/**
 * Automated Setup Script
 * Run this to set up the database and verify configuration
 */

require_once __DIR__ . '/config/database.php';

echo "=== Hamro Digi Cart Setup ===\n\n";

// Check database connection
echo "1. Testing database connection...\n";
try {
    $pdo = getDBConnection();
    echo "   ✓ Database connection successful!\n\n";
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
    echo "   Please check your database credentials in config/database.php\n";
    exit(1);
}

// Check if tables exist
echo "2. Checking database tables...\n";
$tables = ['admins', 'categories', 'products', 'orders', 'pages'];
$existingTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

$missingTables = [];
foreach ($tables as $table) {
    if (in_array($table, $existingTables)) {
        echo "   ✓ Table '$table' exists\n";
    } else {
        echo "   ✗ Table '$table' is missing\n";
        $missingTables[] = $table;
    }
}

if (!empty($missingTables)) {
    echo "\n3. Creating missing tables...\n";
    $schemaFile = __DIR__ . '/database/schema.sql';
    
    if (!file_exists($schemaFile)) {
        echo "   ✗ Schema file not found: $schemaFile\n";
        exit(1);
    }
    
    $sql = file_get_contents($schemaFile);
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^(CREATE DATABASE|USE)/i', $statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Ignore errors for existing tables
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'database') === false) {
                    echo "   ⚠ Warning: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "   ✓ Schema imported\n";
} else {
    echo "\n3. All tables exist - skipping schema import\n";
}

// Check uploads directory
echo "\n4. Checking uploads directory...\n";
$uploadsDir = __DIR__ . '/uploads';
if (!is_dir($uploadsDir)) {
    if (mkdir($uploadsDir, 0755, true)) {
        echo "   ✓ Created uploads directory\n";
    } else {
        echo "   ✗ Failed to create uploads directory\n";
    }
} else {
    echo "   ✓ Uploads directory exists\n";
}

// Check admin user
echo "\n5. Checking admin user...\n";
$stmt = $pdo->query("SELECT COUNT(*) as count FROM admins");
$adminCount = $stmt->fetch()['count'];

if ($adminCount == 0) {
    echo "   ⚠ No admin user found. Creating default admin...\n";
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $hashedPassword, 'admin@hamrodigicart.com']);
    echo "   ✓ Default admin created\n";
    echo "   Username: admin\n";
    echo "   Password: admin123\n";
} else {
    echo "   ✓ Admin user exists\n";
}

// Check categories
echo "\n6. Checking default categories...\n";
$stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
$categoryCount = $stmt->fetch()['count'];

if ($categoryCount == 0) {
    echo "   ⚠ No categories found. Creating default categories...\n";
    $defaultCategories = [
        ['Reels Bundle', 'reels-bundle', 'Premium reels bundle collection'],
        ['WhatsApp Templates', 'whatsapp-templates', 'WhatsApp status templates'],
        ['Digital Planner', 'digital-planner', 'Digital planning tools'],
        ['Social Media Pack', 'social-media-pack', 'Social media content packs'],
        ['Video Templates', 'video-templates', 'Video editing templates']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
    foreach ($defaultCategories as $cat) {
        $stmt->execute($cat);
    }
    echo "   ✓ Default categories created\n";
} else {
    echo "   ✓ Categories exist ($categoryCount found)\n";
}

// Check pages
echo "\n7. Checking default pages...\n";
$stmt = $pdo->query("SELECT COUNT(*) as count FROM pages");
$pageCount = $stmt->fetch()['count'];

if ($pageCount == 0) {
    echo "   ⚠ No pages found. Creating default pages...\n";
    $defaultPages = [
        ['about-us', 'About Us', '<h1>About Us</h1><p>Welcome to Hamro Digi Cart. We provide the best digital products in India.</p>', '/about-us'],
        ['privacy-policy', 'Privacy Policy', '<h1>Privacy Policy</h1><p>Your privacy is important to us.</p>', '/privacy-policy'],
        ['terms-conditions', 'Terms & Conditions', '<h1>Terms & Conditions</h1><p>Please read our terms and conditions.</p>', '/terms-conditions'],
        ['refund-policy', 'Refund Policy', '<h1>Refund Policy</h1><p>Our refund policy details.</p>', '/refund-policy'],
        ['contact-us', 'Contact Us', '<h1>Contact Us</h1><p>Get in touch with us.</p>', '/contact-us']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO pages (page_key, title, content, route) VALUES (?, ?, ?, ?)");
    foreach ($defaultPages as $page) {
        $stmt->execute($page);
    }
    echo "   ✓ Default pages created\n";
} else {
    echo "   ✓ Pages exist ($pageCount found)\n";
}

echo "\n=== Setup Complete! ===\n\n";
echo "Next steps:\n";
echo "1. Start PHP server: php -S localhost:8000\n";
echo "2. Access admin panel: http://localhost:8000/admin/login.php\n";
echo "3. Login with: admin / admin123\n";
echo "4. Change your password in Settings!\n\n";





