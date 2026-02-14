<?php
/**
 * Quick SMTP Test
 * 
 * Simple test to verify SMTP is working on live server.
 * 
 * SECURITY: Delete this file after testing!
 * 
 * Usage: Access via browser or run: php quick-smtp-test.php
 */

// Simple password protection
$TEST_PASSWORD = 'test123'; // CHANGE THIS!

if (php_sapi_name() === 'cli') {
    // Command line mode
    echo "SMTP Quick Test\n";
    echo "===============\n\n";
    
    require_once __DIR__ . '/config/smtp.php';
    
    $settings = getSMTPSettings();
    
    if (empty($settings['smtp_host']) || empty($settings['smtp_email']) || empty($settings['smtp_password'])) {
        echo "❌ SMTP not configured in database.\n";
        exit(1);
    }
    
    echo "SMTP Configuration:\n";
    echo "  Host: " . $settings['smtp_host'] . "\n";
    echo "  Port: " . ($settings['smtp_port'] ?? 587) . "\n";
    echo "  Encryption: " . ($settings['smtp_encryption'] ?? 'tls') . "\n";
    echo "  Email: " . $settings['smtp_email'] . "\n";
    echo "  Password: " . str_repeat('*', min(strlen($settings['smtp_password']), 16)) . "\n\n";
    
    $testEmail = $settings['smtp_email'];
    echo "Sending test email to: $testEmail\n";
    
    $result = sendSMTPEmail(
        $testEmail,
        'SMTP Test - ' . date('Y-m-d H:i:s'),
        '<h2>SMTP Test</h2><p>If you received this email, SMTP is working correctly!</p><p>Time: ' . date('Y-m-d H:i:s') . '</p>'
    );
    
    if ($result['success']) {
        echo "✅ SUCCESS: " . $result['message'] . "\n";
        exit(0);
    } else {
        echo "❌ FAILED: " . $result['error'] . "\n";
        exit(1);
    }
} else {
    // Web mode
    session_start();
    if (!isset($_SESSION['test_auth']) || $_SESSION['test_auth'] !== true) {
        if (isset($_POST['password']) && $_POST['password'] === $TEST_PASSWORD) {
            $_SESSION['test_auth'] = true;
        } else {
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>SMTP Quick Test</title>
                <style>
                    body { font-family: Arial, sans-serif; max-width: 400px; margin: 100px auto; padding: 20px; }
                    input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
                    button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
                    .warning { background: #fff3cd; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
                </style>
            </head>
            <body>
                <div class="warning">
                    <strong>⚠️ SECURITY:</strong> Delete this file after testing!
                </div>
                <h2>SMTP Quick Test</h2>
                <form method="POST">
                    <input type="password" name="password" placeholder="Enter password" required>
                    <button type="submit">Run Test</button>
                </form>
                <p><small>Default password: test123</small></p>
            </body>
            </html>
            <?php
            exit;
        }
    }
    
    require_once __DIR__ . '/config/smtp.php';
    
    $settings = getSMTPSettings();
    $testEmail = $settings['smtp_email'] ?? '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_test'])) {
        if (empty($testEmail)) {
            $result = ['success' => false, 'error' => 'SMTP email not configured'];
        } else {
            $result = sendSMTPEmail(
                $testEmail,
                'SMTP Test - ' . date('Y-m-d H:i:s'),
                '<h2>SMTP Test</h2><p>If you received this email, SMTP is working correctly!</p><p>Time: ' . date('Y-m-d H:i:s') . '</p>'
            );
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>SMTP Quick Test</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 20px auto; padding: 20px; }
            .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
            .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
            .info { background: #d1ecf1; color: #004085; padding: 15px; border-radius: 5px; margin: 10px 0; }
            .warning { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; }
            table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background: #f8f9fa; }
            button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
            button:hover { background: #0056b3; }
        </style>
    </head>
    <body>
        <h1>🔍 SMTP Quick Test</h1>
        
        <div class="warning">
            <strong>⚠️ SECURITY WARNING:</strong> Delete this file immediately after testing!
        </div>
        
        <h2>Current Configuration</h2>
        <table>
            <tr><th>Setting</th><th>Value</th></tr>
            <tr><td>SMTP Host</td><td><?php echo htmlspecialchars($settings['smtp_host'] ?? 'Not set'); ?></td></tr>
            <tr><td>SMTP Port</td><td><?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?></td></tr>
            <tr><td>Encryption</td><td><?php echo htmlspecialchars($settings['smtp_encryption'] ?? 'tls'); ?></td></tr>
            <tr><td>SMTP Email</td><td><?php echo htmlspecialchars($settings['smtp_email'] ?? 'Not set'); ?></td></tr>
            <tr><td>Password</td><td><?php echo str_repeat('*', min(strlen($settings['smtp_password'] ?? ''), 16)); ?></td></tr>
        </table>
        
        <?php if (isset($result)): ?>
            <?php if ($result['success']): ?>
                <div class="success">
                    <strong>✅ SUCCESS!</strong><br>
                    <?php echo htmlspecialchars($result['message']); ?><br>
                    <small>Check your inbox at: <?php echo htmlspecialchars($testEmail); ?></small>
                </div>
            <?php else: ?>
                <div class="error">
                    <strong>❌ FAILED:</strong><br>
                    <?php echo htmlspecialchars($result['error']); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <form method="POST" style="margin-top: 20px;">
            <button type="submit" name="run_test">🧪 Run SMTP Test</button>
        </form>
        
        <div class="info" style="margin-top: 20px;">
            <strong>ℹ️ What this test does:</strong>
            <ul>
                <li>Connects to your SMTP server</li>
                <li>Authenticates with your credentials</li>
                <li>Sends a test email to your SMTP email address</li>
                <li>Shows detailed error messages if something fails</li>
            </ul>
        </div>
    </body>
    </html>
    <?php
}








