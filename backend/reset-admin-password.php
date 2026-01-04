<?php
/**
 * Reset Admin Password
 * Run this if you need to reset the admin password
 */

require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();

// Reset admin password to 'admin123'
$newPassword = 'admin123';
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = 'hamrodigicart1'");
$stmt->execute([$hashedPassword]);

echo "✓ Admin password reset successfully!\n";
echo "Username: hamrodigicart1\n";
echo "Password: admin123\n\n";
echo "You can now login at: http://localhost:8000/admin/login.php\n";





