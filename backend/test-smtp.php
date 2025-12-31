<?php
/**
 * SMTP Test Script
 * Use this to debug SMTP connection issues
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/smtp.php';

// Only allow from localhost or if admin is logged in
if (!isAdminLoggedIn() && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die('Access denied');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>SMTP Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>SMTP Configuration Test</h1>
        
        <?php
        echo '<div class="info"><strong>Testing SMTP Configuration...</strong></div>';
        
        // Get SMTP settings
        try {
            $smtpSettings = getSMTPSettings();
            
            echo '<h2>Current SMTP Settings:</h2>';
            echo '<pre>';
            echo 'Host: ' . (isset($smtpSettings['smtp_host']) ? htmlspecialchars($smtpSettings['smtp_host']) : 'NOT SET') . "\n";
            echo 'Port: ' . (isset($smtpSettings['smtp_port']) ? htmlspecialchars($smtpSettings['smtp_port']) : 'NOT SET (default: 587)') . "\n";
            echo 'Encryption: ' . (isset($smtpSettings['smtp_encryption']) ? htmlspecialchars($smtpSettings['smtp_encryption']) : 'NOT SET (default: tls)') . "\n";
            echo 'Email: ' . (isset($smtpSettings['smtp_email']) ? htmlspecialchars($smtpSettings['smtp_email']) : 'NOT SET') . "\n";
            echo 'Password: ' . (isset($smtpSettings['smtp_password']) ? (strlen($smtpSettings['smtp_password']) > 0 ? str_repeat('*', strlen($smtpSettings['smtp_password'])) : 'EMPTY') : 'NOT SET') . "\n";
            echo 'From Name: ' . (isset($smtpSettings['smtp_from_name']) ? htmlspecialchars($smtpSettings['smtp_from_name']) : 'NOT SET') . "\n";
            echo '</pre>';
            
            // Check if all required settings are present
            $missing = [];
            if (empty($smtpSettings['smtp_host'])) $missing[] = 'SMTP Host';
            if (empty($smtpSettings['smtp_email'])) $missing[] = 'SMTP Email';
            if (empty($smtpSettings['smtp_password'])) $missing[] = 'SMTP Password';
            
            if (!empty($missing)) {
                echo '<div class="error"><strong>Missing Settings:</strong> ' . implode(', ', $missing) . '</div>';
                echo '<p>Please configure these settings in Admin Panel → Settings → SMTP Email Settings</p>';
            } else {
                echo '<div class="success">✓ All required settings are configured</div>';
                
                // Test email sending
                if (isset($_GET['test']) && $_GET['test'] === 'send') {
                    $testEmail = $smtpSettings['smtp_email'];
                    echo '<h2>Testing Email Send...</h2>';
                    echo '<p>Sending test email to: <strong>' . htmlspecialchars($testEmail) . '</strong></p>';
                    
                    $result = sendSMTPEmail(
                        $testEmail,
                        'SMTP Test Email',
                        '<h2>Test Email</h2><p>If you received this email, your SMTP configuration is working correctly!</p>'
                    );
                    
                    if ($result['success']) {
                        echo '<div class="success"><strong>✓ SUCCESS!</strong> Email sent successfully!</div>';
                        echo '<p>Check your inbox at: <strong>' . htmlspecialchars($testEmail) . '</strong></p>';
                    } else {
                        echo '<div class="error"><strong>✗ FAILED:</strong> ' . htmlspecialchars($result['error']) . '</div>';
                    }
                } else {
                    echo '<h2>Ready to Test</h2>';
                    echo '<p><a href="?test=send" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Send Test Email</a></p>';
                }
            }
            
        } catch (Exception $e) {
            echo '<div class="error"><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
        
        <h2>Common Issues & Solutions:</h2>
        <ul>
            <li><strong>Gmail App Password:</strong> Make sure you're using an App Password (16 characters, no spaces), not your regular Gmail password. Enable 2-Step Verification first.</li>
            <li><strong>Port & Encryption:</strong> For Gmail, use Port 587 with TLS, or Port 465 with SSL.</li>
            <li><strong>Firewall:</strong> Make sure your server can connect to the SMTP server (outbound port 587 or 465).</li>
            <li><strong>PHP Extensions:</strong> Make sure OpenSSL extension is enabled in PHP.</li>
        </ul>
    </div>
</body>
</html>





