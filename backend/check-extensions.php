<?php
/**
 * Check if MySQL extensions are loaded
 */
header('Content-Type: text/plain');

echo "=== PHP MySQL Extensions Check ===\n\n";

echo "PHP Version: " . phpversion() . "\n";
echo "Loaded php.ini: " . php_ini_loaded_file() . "\n\n";

echo "Checking extensions:\n";
echo "-------------------\n";

$extensions = ['pdo', 'pdo_mysql', 'mysqli', 'mysqlnd'];

foreach ($extensions as $ext) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? '✓ LOADED' : '✗ NOT LOADED';
    echo "$ext: $status\n";
}

echo "\n";

if (!extension_loaded('pdo_mysql')) {
    echo "ERROR: pdo_mysql extension is not loaded!\n";
    echo "\nTo fix this:\n";
    echo "1. Open: " . php_ini_loaded_file() . "\n";
    echo "2. Find and uncomment (remove ;):\n";
    echo "   extension=pdo_mysql\n";
    echo "   extension=mysqli\n";
    echo "3. Also uncomment:\n";
    echo "   extension_dir = \"ext\"\n";
    echo "4. Restart your PHP server\n";
} else {
    echo "✓ All MySQL extensions are loaded!\n";
    echo "\nTesting database connection...\n";
    
    try {
        require_once __DIR__ . '/config/database.php';
        $pdo = getDBConnection();
        echo "✓ Database connection successful!\n";
    } catch (Exception $e) {
        echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    }
}





