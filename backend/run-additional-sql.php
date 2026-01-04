<?php
/**
 * Run Additional SQL Files
 * Executes additional SQL migration files
 */

require_once __DIR__ . '/config/database.php';

echo "=== Running Additional SQL Files ===\n\n";

try {
    $pdo = getDBConnection();
    echo "✓ Database connection successful!\n\n";
} catch (Exception $e) {
    die("✗ Database connection failed: " . $e->getMessage() . "\n");
}

// List of additional SQL files to run
$sqlFiles = [
    'add_offers_table.sql',
    'add_product_link.sql',
    'add_navigation_menu.sql'
];

foreach ($sqlFiles as $sqlFile) {
    $filePath = __DIR__ . '/database/' . $sqlFile;
    
    if (!file_exists($filePath)) {
        echo "⚠ File not found: $sqlFile\n";
        continue;
    }
    
    echo "Running: $sqlFile...\n";
    
    $sql = file_get_contents($filePath);
    
    // Split by semicolons and execute
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (empty($statement)) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
        } catch (PDOException $e) {
            // Ignore errors for existing tables/columns
            if (strpos($e->getMessage(), 'already exists') === false && 
                strpos($e->getMessage(), 'Duplicate column') === false &&
                strpos($e->getMessage(), 'Duplicate entry') === false) {
                echo "  ⚠ Warning: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "  ✓ Completed: $sqlFile\n\n";
}

echo "=== All SQL Files Executed ===\n\n";

// Verify tables
echo "Verifying tables...\n";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Tables found: " . implode(', ', $tables) . "\n\n";






