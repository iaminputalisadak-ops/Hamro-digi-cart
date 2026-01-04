<?php
/**
 * Database Import Script
 * Imports database data from SQL export file
 * 
 * Usage: php import_database.php [export_file.sql]
 */

require_once __DIR__ . '/../config/database.php';

// Get export file
$exportFile = isset($argv[1]) ? $argv[1] : null;

if (!$exportFile) {
    // Find the most recent export file
    $files = glob(__DIR__ . '/database_export_*.sql');
    if (empty($files)) {
        die("Error: No export file found. Please specify a file:\n  php import_database.php database_export_2024-01-01_120000.sql\n\n");
    }
    
    // Sort by modification time, get most recent
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    
    $exportFile = $files[0];
    echo "Using most recent export file: " . basename($exportFile) . "\n\n";
}

if (!file_exists($exportFile)) {
    die("Error: Export file not found: $exportFile\n\n");
}

echo "Importing database from: " . basename($exportFile) . "\n";
echo "Database: " . DB_NAME . "\n\n";

// First, connect to MySQL server (without selecting database)
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
    $pdoServer = new PDO($dsn, DB_USER, DB_PASS);
    $pdoServer->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists
    $stmt = $pdoServer->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    $dbExists = $stmt->rowCount() > 0;
    
    if (!$dbExists) {
        echo "Database '" . DB_NAME . "' does not exist. Creating it...\n";
        $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✓ Database created successfully!\n\n";
    } else {
        echo "✓ Database '" . DB_NAME . "' already exists.\n\n";
    }
    
    // Now connect to the specific database
    $pdo = getDBConnection();
    
} catch (PDOException $e) {
    die("Error connecting to MySQL server: " . $e->getMessage() . "\n" .
        "Please check your database credentials in backend/config/database.php\n\n");
}

// Read SQL file
$sql = file_get_contents($exportFile);

if ($sql === false) {
    die("Error: Cannot read export file.\n\n");
}

// Disable foreign key checks temporarily
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");

// Split SQL into individual statements
// Remove comments and empty lines
$sql = preg_replace('/--.*$/m', '', $sql);
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    function($stmt) {
        return !empty($stmt);
    }
);

$successCount = 0;
$errorCount = 0;

echo "Executing SQL statements...\n";

foreach ($statements as $statement) {
    if (empty(trim($statement))) {
        continue;
    }
    
    try {
        $pdo->exec($statement);
        $successCount++;
        
        // Show progress for large imports
        if ($successCount % 10 == 0) {
            echo ".";
        }
    } catch (PDOException $e) {
        $errorCount++;
        echo "\nWarning: " . $e->getMessage() . "\n";
    }
}

// Re-enable foreign key checks
$pdo->exec("SET FOREIGN_KEY_CHECKS=1");

echo "\n\n✓ Database import completed!\n";
echo "Successful statements: $successCount\n";
if ($errorCount > 0) {
    echo "Errors: $errorCount\n";
}
echo "\nYour database has been restored with all data.\n\n";

