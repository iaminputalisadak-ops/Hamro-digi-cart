<?php
/**
 * Test Order Email Notification
 * This script tests if order email notifications are working
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/smtp.php';
require_once __DIR__ . '/api/orders.php';

echo "=== Testing Order Email Notification ===\n\n";

// Check if function exists
if (!function_exists('sendNewOrderNotificationToAdmin')) {
    echo "❌ ERROR: sendNewOrderNotificationToAdmin function not found!\n";
    echo "This function should be defined in backend/api/orders.php\n\n";
    exit(1);
} else {
    echo "✓ Function sendNewOrderNotificationToAdmin exists\n\n";
}

// Check admin email configuration
$pdo = getDBConnection();

echo "1. Checking Admin Email Configuration...\n";
$stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'contact_email'");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$contactEmail = $result ? trim($result['setting_value']) : '';

if (empty($contactEmail)) {
    echo "   ⚠ Contact Email not set, checking SMTP email...\n";
    $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'smtp_email'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $smtpEmail = $result ? trim($result['setting_value']) : '';
    
    if (empty($smtpEmail)) {
        echo "   ❌ ERROR: No admin email configured!\n";
        echo "   Please set contact_email in Website Settings or configure SMTP email.\n\n";
        exit(1);
    } else {
        echo "   ✓ Using SMTP email: $smtpEmail\n";
        $adminEmail = $smtpEmail;
    }
} else {
    echo "   ✓ Contact Email: $contactEmail\n";
    $adminEmail = $contactEmail;
}

echo "\n2. Checking SMTP Configuration...\n";
$smtpSettings = getSMTPSettings();

$required = ['smtp_host', 'smtp_email', 'smtp_password'];
$missing = [];

foreach ($required as $key) {
    if (empty($smtpSettings[$key])) {
        $missing[] = $key;
        echo "   ❌ Missing: $key\n";
    } else {
        echo "   ✓ $key: " . ($key === 'smtp_password' ? '***' : $smtpSettings[$key]) . "\n";
    }
}

if (!empty($missing)) {
    echo "\n   ❌ ERROR: SMTP not fully configured!\n";
    echo "   Missing: " . implode(', ', $missing) . "\n";
    echo "   Please configure SMTP in Admin Panel → Settings\n\n";
    exit(1);
}

echo "\n3. Creating Test Order Data...\n";
// Get a real product for testing
$stmt = $pdo->query("SELECT id, title, price FROM products LIMIT 1");
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "   ❌ ERROR: No products found in database!\n";
    echo "   Please add at least one product first.\n\n";
    exit(1);
}

echo "   ✓ Using product: {$product['title']} (ID: {$product['id']})\n";

// Create test order data
$testOrder = [
    'id' => 999999, // Test order ID
    'product_id' => $product['id'],
    'product_title' => $product['title'],
    'product_price' => $product['price'],
    'customer_name' => 'Test Customer',
    'customer_email' => 'test@example.com',
    'customer_phone' => '1234567890',
    'total_amount' => $product['price'],
    'payment_screenshot' => '',
    'status' => 'pending',
    'created_at' => date('Y-m-d H:i:s')
];

echo "\n4. Testing Email Sending...\n";
echo "   Sending test notification to: $adminEmail\n";

$result = sendNewOrderNotificationToAdmin($testOrder);

if ($result['success']) {
    echo "\n   ✅ SUCCESS! Email sent successfully!\n";
    echo "   Message: " . ($result['message'] ?? 'Email sent') . "\n";
    if (isset($result['to'])) {
        echo "   Sent to: " . $result['to'] . "\n";
    }
} else {
    echo "\n   ❌ FAILED! Email could not be sent.\n";
    echo "   Error: " . ($result['error'] ?? 'Unknown error') . "\n";
    echo "\n   Troubleshooting:\n";
    echo "   1. Check SMTP settings in Admin Panel → Settings\n";
    echo "   2. Verify SMTP credentials are correct\n";
    echo "   3. For Gmail, ensure you're using an App Password\n";
    echo "   4. Check if port 587 (TLS) or 465 (SSL) is blocked\n";
    echo "   5. Check PHP error logs for more details\n";
    exit(1);
}

echo "\n=== Test Complete ===\n";
echo "If you received the email, the notification system is working correctly!\n";
echo "When a real order is placed, you will receive a similar notification.\n\n";







