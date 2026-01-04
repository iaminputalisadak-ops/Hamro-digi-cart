<?php
/**
 * Test Admin Email Notification
 * This page helps debug email notification issues
 */
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$pdo = getDBConnection();
$message = '';
$error = '';
$testResult = '';

// Handle test email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
    require_once __DIR__ . '/../config/smtp.php';
    
    // Get admin email
    $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'contact_email'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $adminEmail = $result ? trim($result['setting_value']) : '';
    
    if (empty($adminEmail)) {
        $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'smtp_email'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $adminEmail = $result ? trim($result['setting_value']) : '';
    }
    
    if (empty($adminEmail)) {
        $error = 'No admin email configured. Please set contact_email in Website Settings or configure SMTP email.';
    } else {
        // Check SMTP settings
        $smtpSettings = getSMTPSettings();
        if (empty($smtpSettings['smtp_host']) || empty($smtpSettings['smtp_email']) || empty($smtpSettings['smtp_password'])) {
            $error = 'SMTP not configured. Please configure SMTP settings in Settings page.';
        } else {
            // Send test email
            $testMessage = '<html><body><h2>Test Email from Hamro Digi Cart</h2><p>This is a test email to verify that admin notifications are working correctly.</p><p>If you received this email, the notification system is configured properly.</p></body></html>';
            $result = sendSMTPEmail($adminEmail, 'Test: Admin Notification System', $testMessage);
            
            if ($result['success']) {
                $message = 'Test email sent successfully to: ' . htmlspecialchars($adminEmail);
            } else {
                $error = 'Failed to send test email: ' . ($result['error'] ?? 'Unknown error');
            }
        }
    }
}

// Get current settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('contact_email', 'smtp_email', 'smtp_host', 'smtp_port', 'smtp_encryption')");
$settingsData = $stmt->fetchAll();
$settings = [];
foreach ($settingsData as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Admin Email - Admin Panel</title>
    <?php include 'includes/favicon.php'; ?>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <h1>🧪 Test Admin Email Notification</h1>
                <p style="color: #666; margin-bottom: 20px;">Test if admin email notifications are working correctly.</p>
                
                <?php if ($message): ?>
                    <div style="background: #d4edda; color: #155724; padding: 15px; margin: 20px 0; border-radius: 5px;">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 5px;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <div class="data-table" style="max-width: 800px; margin-bottom: 30px;">
                    <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">📧 Current Email Configuration</h2>
                    <div style="padding: 20px;">
                        <div class="form-group">
                            <label>Admin Email (Contact Email):</label>
                            <input type="text" value="<?php echo htmlspecialchars($settings['contact_email'] ?? 'Not set'); ?>" readonly style="background: #f5f5f5;">
                            <small style="color: #666;">Set in Website Settings → Contact Email</small>
                        </div>
                        
                        <div class="form-group">
                            <label>SMTP Email:</label>
                            <input type="text" value="<?php echo htmlspecialchars($settings['smtp_email'] ?? 'Not set'); ?>" readonly style="background: #f5f5f5;">
                            <small style="color: #666;">Set in Settings → SMTP Email</small>
                        </div>
                        
                        <div class="form-group">
                            <label>SMTP Host:</label>
                            <input type="text" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? 'Not set'); ?>" readonly style="background: #f5f5f5;">
                        </div>
                        
                        <div class="form-group">
                            <label>SMTP Port:</label>
                            <input type="text" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? 'Not set'); ?>" readonly style="background: #f5f5f5;">
                        </div>
                        
                        <div class="form-group">
                            <label>SMTP Encryption:</label>
                            <input type="text" value="<?php echo htmlspecialchars($settings['smtp_encryption'] ?? 'Not set'); ?>" readonly style="background: #f5f5f5;">
                        </div>
                    </div>
                </div>
                
                <div class="data-table" style="max-width: 800px;">
                    <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">🔍 Email Notification Status</h2>
                    <div style="padding: 20px;">
                        <?php
                        $adminEmail = $settings['contact_email'] ?? $settings['smtp_email'] ?? '';
                        $smtpConfigured = !empty($settings['smtp_host']) && !empty($settings['smtp_email']) && !empty($settings['smtp_password'] ?? '');
                        
                        if (empty($adminEmail)) {
                            echo '<div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px;">';
                            echo '<strong>⚠️ Admin Email Not Configured</strong><br>';
                            echo 'Please set the Contact Email in <a href="website-settings.php">Website Settings</a> or configure SMTP Email in <a href="settings.php">Settings</a>.';
                            echo '</div>';
                        } else {
                            echo '<div style="background: #d1fae5; border-left: 4px solid #22c55e; padding: 15px; margin: 20px 0; border-radius: 4px;">';
                            echo '<strong>✅ Admin Email Configured:</strong> ' . htmlspecialchars($adminEmail);
                            echo '</div>';
                        }
                        
                        if (!$smtpConfigured) {
                            echo '<div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px;">';
                            echo '<strong>⚠️ SMTP Not Configured</strong><br>';
                            echo 'Please configure SMTP settings in <a href="settings.php">Settings</a> to enable email notifications.';
                            echo '</div>';
                        } else {
                            echo '<div style="background: #d1fae5; border-left: 4px solid #22c55e; padding: 15px; margin: 20px 0; border-radius: 4px;">';
                            echo '<strong>✅ SMTP Configured</strong>';
                            echo '</div>';
                        }
                        ?>
                        
                        <form method="POST" style="margin-top: 30px;">
                            <button type="submit" name="test_email" class="btn btn-primary">📧 Send Test Email</button>
                        </form>
                        
                        <div style="background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 4px;">
                            <strong>ℹ️ How It Works:</strong>
                            <ol style="margin: 10px 0; padding-left: 20px;">
                                <li>When a customer submits a payment, an email is automatically sent to the admin email</li>
                                <li>The email includes order details, customer information, and payment screenshot</li>
                                <li>Admin email is taken from "Contact Email" in Website Settings, or SMTP Email as fallback</li>
                                <li>Make sure SMTP is properly configured in Settings page</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/admin.js"></script>
</body>
</html>


