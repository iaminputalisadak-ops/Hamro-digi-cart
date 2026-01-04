<?php
/**
 * Check Admin User
 * Run this to check if admin user exists and verify credentials
 */

require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();

try {
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT id, username, email, password FROM admins WHERE username = ?");
    $stmt->execute(['hamrodigicart1']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "✓ Admin user found!\n";
        echo "==================\n";
        echo "ID: " . $admin['id'] . "\n";
        echo "Username: " . $admin['username'] . "\n";
        echo "Email: " . $admin['email'] . "\n";
        echo "Password Hash: " . substr($admin['password'], 0, 30) . "...\n\n";
        
        // Test password verification
        $testPassword = 'admin123';
        if (password_verify($testPassword, $admin['password'])) {
            echo "✓ Password verification: SUCCESS\n";
            echo "The password 'admin123' is CORRECT!\n\n";
        } else {
            echo "✗ Password verification: FAILED\n";
            echo "The password 'admin123' does NOT match!\n";
            echo "Resetting password...\n\n";
            
            // Reset password
            $hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
            $stmt->execute([$hashedPassword, 'hamrodigicart1']);
            
            echo "✓ Password reset successfully!\n";
            echo "Username: hamrodigicart1\n";
            echo "Password: admin123\n\n";
        }
    } else {
        echo "✗ Admin user 'hamrodigicart1' NOT found!\n";
        echo "Creating admin user...\n\n";
        
        // Create admin user
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['hamrodigicart1', $hashedPassword, 'admin@hamrodigicart.com']);
        
        echo "✓ Admin user created successfully!\n";
        echo "Username: hamrodigicart1\n";
        echo "Password: admin123\n\n";
    }
    
    echo "Login URL: http://localhost:8000/admin/login.php\n";
    
} catch (PDOException $e) {
    echo "✗ Database Error: " . $e->getMessage() . "\n";
    exit(1);
}











