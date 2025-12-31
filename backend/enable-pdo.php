<?php
/**
 * Enable PDO MySQL Extension Helper
 */

echo "=== PDO MySQL Extension Helper ===\n\n";

// Check if extension is loaded
if (extension_loaded('pdo_mysql')) {
    echo "✓ PDO MySQL extension is already enabled!\n";
    exit(0);
}

echo "✗ PDO MySQL extension is not enabled.\n\n";

// Try to find php.ini
$phpIni = php_ini_loaded_file();
$phpIniScanned = php_ini_scanned_files();

echo "PHP Configuration:\n";
echo "  PHP Version: " . PHP_VERSION . "\n";

if ($phpIni) {
    echo "  Loaded php.ini: $phpIni\n";
} else {
    echo "  Loaded php.ini: (none)\n";
}

if ($phpIniScanned) {
    echo "  Scanned ini files: $phpIniScanned\n";
} else {
    echo "  Scanned ini files: (none)\n";
}

// Common Windows PHP paths
$commonPaths = [
    'C:\\php\\php.ini',
    'C:\\xampp\\php\\php.ini',
    'C:\\wamp\\bin\\php\\php' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '\\php.ini',
    'C:\\Program Files\\PHP\\php.ini',
    'C:\\Program Files (x86)\\PHP\\php.ini',
];

echo "\nSearching for php.ini in common locations...\n";
$foundIni = null;

foreach ($commonPaths as $path) {
    if (file_exists($path)) {
        echo "  ✓ Found: $path\n";
        $foundIni = $path;
        break;
    }
}

if (!$foundIni && $phpIni) {
    $foundIni = $phpIni;
}

if ($foundIni) {
    echo "\nAttempting to enable extension in: $foundIni\n";
    
    $iniContent = file_get_contents($foundIni);
    $originalContent = $iniContent;
    
    // Try to enable pdo_mysql
    $patterns = [
        '/;extension\s*=\s*pdo_mysql/i',
        '/;extension\s*=\s*php_pdo_mysql/i',
    ];
    
    $modified = false;
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $iniContent)) {
            $iniContent = preg_replace($pattern, 'extension=pdo_mysql', $iniContent);
            $modified = true;
            break;
        }
    }
    
    // If not found, try to add it
    if (!$modified && !preg_match('/extension\s*=\s*pdo_mysql/i', $iniContent)) {
        // Find extension section
        if (preg_match('/\[Extension\]/i', $iniContent)) {
            $iniContent = preg_replace('/(\[Extension\])/i', "$1\nextension=pdo_mysql", $iniContent);
            $modified = true;
        } else {
            // Add at end
            $iniContent .= "\n; PDO MySQL Extension\nextension=pdo_mysql\n";
            $modified = true;
        }
    }
    
    if ($modified && $iniContent !== $originalContent) {
        // Backup original
        $backupFile = $foundIni . '.backup.' . date('YmdHis');
        file_put_contents($backupFile, $originalContent);
        echo "  ✓ Created backup: $backupFile\n";
        
        // Write modified content
        if (file_put_contents($foundIni, $iniContent)) {
            echo "  ✓ Updated php.ini successfully!\n";
            echo "\n⚠ IMPORTANT: You must restart your PHP server for changes to take effect!\n";
            echo "  Stop the current server (Ctrl+C) and start it again.\n";
        } else {
            echo "  ✗ Failed to write php.ini (may need administrator privileges)\n";
            echo "\nPlease manually edit: $foundIni\n";
            echo "Find: ;extension=pdo_mysql\n";
            echo "Change to: extension=pdo_mysql\n";
        }
    } else {
        echo "  ℹ Extension line not found or already enabled\n";
        echo "  Please manually check: $foundIni\n";
    }
} else {
    echo "\n✗ Could not find php.ini automatically.\n";
    echo "\nPlease manually enable the extension:\n";
    echo "1. Find your php.ini file\n";
    echo "2. Search for: ;extension=pdo_mysql\n";
    echo "3. Remove the semicolon: extension=pdo_mysql\n";
    echo "4. Save the file\n";
    echo "5. Restart PHP server\n";
}

echo "\n";





