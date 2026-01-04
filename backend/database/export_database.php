<?php
/**
 * Database Export Script
 * Exports all database data to SQL file for easy transfer
 * 
 * Usage: php export_database.php
 */

require_once __DIR__ . '/../config/database.php';

// Get database connection
$pdo = getDBConnection();

// Output file
$outputFile = __DIR__ . '/database_export_' . date('Y-m-d_His') . '.sql';

// Open file for writing
$file = fopen($outputFile, 'w');

if (!$file) {
    die("Error: Cannot create export file.\n");
}

// Write header
fwrite($file, "-- Database Export\n");
fwrite($file, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
fwrite($file, "-- Database: " . DB_NAME . "\n\n");
fwrite($file, "SET FOREIGN_KEY_CHECKS=0;\n\n");

// Tables to export
$tables = [
    'admins',
    'categories',
    'products',
    'orders',
    'pages',
    'settings',
    'offers'
];

foreach ($tables as $table) {
    echo "Exporting table: $table\n";
    
    // Get table structure
    $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
    $createTable = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($createTable) {
        fwrite($file, "-- Table structure for `$table`\n");
        fwrite($file, "DROP TABLE IF EXISTS `$table`;\n");
        fwrite($file, $createTable['Create Table'] . ";\n\n");
    }
    
    // Get table data
    $stmt = $pdo->query("SELECT * FROM `$table`");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($rows) > 0) {
        fwrite($file, "-- Data for table `$table`\n");
        fwrite($file, "INSERT INTO `$table` VALUES\n");
        
        $values = [];
        foreach ($rows as $row) {
            $rowValues = [];
            foreach ($row as $key => $value) {
                if ($value === null) {
                    $rowValues[] = 'NULL';
                } else {
                    // Escape special characters
                    $value = $pdo->quote($value);
                    $rowValues[] = $value;
                }
            }
            $values[] = '(' . implode(',', $rowValues) . ')';
        }
        
        fwrite($file, implode(",\n", $values) . ";\n\n");
    } else {
        fwrite($file, "-- No data in table `$table`\n\n");
    }
}

fwrite($file, "SET FOREIGN_KEY_CHECKS=1;\n");

fclose($file);

echo "\n✓ Database export completed!\n";
echo "Export file: $outputFile\n";
echo "File size: " . number_format(filesize($outputFile) / 1024, 2) . " KB\n\n";
echo "You can now include this file when transferring the project.\n";
echo "To import on another device, use: php import_database.php\n\n";

