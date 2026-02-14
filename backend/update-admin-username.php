<?php
/**
 * Update Admin Username
 * Run this to change the admin username from 'admin' to 'hamrodigicart1'
 */

require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();

try {
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT id, username FROM admins WHERE username = ?");
    $stmt->execute(['admin']);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        // Check if new username already exists
        $stmt = $pdo->prepare("SELECT id, username FROM admins WHERE username = ?");
        $stmt->execute(['hamrodigicart1']);
        $newAdmin = $stmt->fetch();
        
        if ($newAdmin) {
            echo "✓ Username 'hamrodigicart1' already exists in database.\n";
            echo "Username: hamrodigicart1\n";
            echo "Password: admin123\n\n";
            exit(0);
        } else {
            echo "✗ No admin user found with username 'admin'.\n";
            echo "Please create an admin user first or check your database.\n\n";
            exit(1);
        }
    }
    
    // Check if new username already exists
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->execute(['hamrodigicart1']);
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo "⚠ Username 'hamrodigicart1' already exists.\n";
        echo "Skipping update. Current username is already: hamrodigicart1\n\n";
        exit(0);
    }
    
    // Update username
    $stmt = $pdo->prepare("UPDATE admins SET username = ? WHERE username = ?");
    $stmt->execute(['hamrodigicart1', 'admin']);
    
    echo "✓ Admin username updated successfully!\n";
    echo "Old username: admin\n";
    echo "New username: hamrodigicart1\n";
    echo "Password: admin123 (unchanged)\n\n";
    echo "You can now login at: http://localhost:8000/admin/login.php\n";
    echo "Login with: hamrodigicart1 / admin123\n\n";
    
} catch (PDOException $e) {
    echo "✗ Error updating username: " . $e->getMessage() . "\n\n";
    exit(1);
}














