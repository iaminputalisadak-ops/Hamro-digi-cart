<?php
/**
 * Installation Script
 * Run this once to set up the database
 */

require_once __DIR__ . '/config/database.php';

// Check if already installed
$pdo = getDBConnection();
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

if (count($tables) > 0) {
    die("Database already installed. Tables found: " . implode(', ', $tables));
}

// Read and execute schema
$schemaFile = __DIR__ . '/database/schema.sql';
if (!file_exists($schemaFile)) {
    die("Schema file not found: $schemaFile");
}

$sql = file_get_contents($schemaFile);

// Split by semicolons and execute
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (!empty($statement) && !preg_match('/^(CREATE DATABASE|USE)/i', $statement)) {
        try {
            $pdo->exec($statement);
        } catch (PDOException $e) {
            // Ignore errors for CREATE DATABASE and USE statements
            if (strpos($e->getMessage(), 'database') === false) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }
    }
}

echo "Installation completed successfully!\n";
echo "Default admin credentials:\n";
echo "Username: admin\n";
echo "Password: admin123\n";
echo "\nPlease change the password after first login!\n";





