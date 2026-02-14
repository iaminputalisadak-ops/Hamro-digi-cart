<?php
/**
 * Quick Database Connection Test
 * Run this to verify database connection is working
 */

require_once __DIR__ . '/backend/config/database.php';

echo "=== Database Connection Test ===\n\n";

// Test 1: Check PDO MySQL extension
echo "1. Checking PDO MySQL extension...\n";
if (extension_loaded('pdo_mysql')) {
    echo "   ✓ PDO MySQL extension is loaded\n\n";
} else {
    echo "   ✗ PDO MySQL extension is NOT loaded\n\n";
    exit(1);
}

// Test 2: Database connection
echo "2. Testing database connection...\n";
try {
    $pdo = getDBConnection();
    echo "   ✓ Database connection successful!\n\n";
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 3: Get database info
echo "3. Database information...\n";
try {
    $stmt = $pdo->query("SELECT DATABASE() as db_name, VERSION() as version");
    $info = $stmt->fetch();
    echo "   Database Name: " . $info['db_name'] . "\n";
    echo "   MySQL Version: " . $info['version'] . "\n\n";
} catch (Exception $e) {
    echo "   ⚠ Could not get database info: " . $e->getMessage() . "\n\n";
}

// Test 4: Check tables
echo "4. Checking database tables...\n";
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "   ✓ Found " . count($tables) . " tables: " . implode(', ', $tables) . "\n\n";
} catch (Exception $e) {
    echo "   ⚠ Could not check tables: " . $e->getMessage() . "\n\n";
}

echo "=== All Tests Passed! ===\n";
echo "Database connection is working correctly.\n";
















