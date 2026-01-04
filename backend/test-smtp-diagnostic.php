<?php
/**
 * SMTP Diagnostic Tool
 * 
 * This script helps diagnose SMTP issues on your live server.
 * Upload this file to your server and access it via browser.
 * 
 * SECURITY: Delete this file after testing!
 */

// Set a simple password to protect this diagnostic tool
$DIAGNOSTIC_PASSWORD = 'test123'; // CHANGE THIS or delete file after use!

// Simple password protection
session_start();
if (!isset($_SESSION['authenticated'])) {
    if (isset($_POST['password']) && $_POST['password'] === $DIAGNOSTIC_PASSWORD) {
        $_SESSION['authenticated'] = true;
    } else {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>SMTP Diagnostic - Login</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 400px; margin: 100px auto; padding: 20px; }
                input { width: 100%; padding: 10px; margin: 10px 0; }
                button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
            </style>
        </head>
        <body>
            <h2>SMTP Diagnostic Tool</h2>
            <p><strong>⚠️ SECURITY WARNING:</strong> Delete this file after testing!</p>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter password" required>
                <button type="submit">Access Diagnostic Tool</button>
            </form>
        </body>
        </html>
        <?php
        exit;
    } else {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>SMTP Diagnostic - Login</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 400px; margin: 100px auto; padding: 20px; }
                input { width: 100%; padding: 10px; margin: 10px 0; }
                button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
                .error { color: red; }
            </style>
        </head>
        <body>
            <h2>SMTP Diagnostic Tool</h2>
            <p class="error">Invalid password. Default: test123</p>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter password" required>
                <button type="submit">Access Diagnostic Tool</button>
            </form>
        </body>
        </html>
        <?php
        exit;
    }
}

// Main diagnostic page
?>
<!DOCTYPE html>
<html>
<head>
    <title>SMTP Diagnostic Tool</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { color: #004085; background: #d1ecf1; padding: 10px; border-radius: 4px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        input, select { width: 100%; padding: 8px; margin: 5px 0; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .test-form { background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 SMTP Diagnostic Tool</h1>
    <div class="warning">
        <strong>⚠️ SECURITY WARNING:</strong> Delete this file immediately after testing! It contains diagnostic information that could be sensitive.
    </div>

    <?php
    // Test 1: PHP Extensions
    echo '<div class="section">';
    echo '<h2>1. PHP Extensions Check</h2>';
    
    $openssl = extension_loaded('openssl');
    $sockets = function_exists('stream_socket_client');
    $curl = extension_loaded('curl');
    
    echo '<table>';
    echo '<tr><th>Extension</th><th>Status</th><th>Required</th></tr>';
    echo '<tr><td>OpenSSL</td><td>' . ($openssl ? '<span class="success">✅ Enabled</span>' : '<span class="error">❌ Disabled</span>') . '</td><td>Required for TLS/SSL</td></tr>';
    echo '<tr><td>Socket Functions</td><td>' . ($sockets ? '<span class="success">✅ Available</span>' : '<span class="error">❌ Disabled</span>') . '</td><td>Required for SMTP</td></tr>';
    echo '<tr><td>cURL</td><td>' . ($curl ? '<span class="success">✅ Enabled</span>' : '<span class="warning">⚠️ Optional</span>') . '</td><td>Not required</td></tr>';
    echo '</table>';
    
    echo '<p><strong>PHP Version:</strong> ' . phpversion() . '</p>';
    
    if (!$openssl) {
        echo '<div class="error"><strong>CRITICAL:</strong> OpenSSL extension is not enabled. Enable it in php.ini: extension=openssl</div>';
    }
    
    if (!$sockets) {
        echo '<div class="error"><strong>CRITICAL:</strong> Socket functions are disabled. Contact your hosting provider.</div>';
    }
    
    echo '</div>';

    // Test 2: PHP Configuration
    echo '<div class="section">';
    echo '<h2>2. PHP Configuration</h2>';
    
    echo '<table>';
    echo '<tr><th>Setting</th><th>Value</th><th>Status</th></tr>';
    
    $allow_url_fopen = ini_get('allow_url_fopen');
    echo '<tr><td>allow_url_fopen</td><td>' . ($allow_url_fopen ? 'On' : 'Off') . '</td><td>' . ($allow_url_fopen ? '<span class="success">✅</span>' : '<span class="warning">⚠️ Usually OK</span>') . '</td></tr>';
    
    $max_execution_time = ini_get('max_execution_time');
    echo '<tr><td>max_execution_time</td><td>' . $max_execution_time . ' seconds</td><td>' . ($max_execution_time >= 30 ? '<span class="success">✅</span>' : '<span class="warning">⚠️ May be too low</span>') . '</td></tr>';
    
    $default_socket_timeout = ini_get('default_socket_timeout');
    echo '<tr><td>default_socket_timeout</td><td>' . $default_socket_timeout . ' seconds</td><td>' . ($default_socket_timeout >= 30 ? '<span class="success">✅</span>' : '<span class="warning">⚠️ May be too low</span>') . '</td></tr>';
    
    echo '</table>';
    echo '</div>';

    // Test 3: Port Connectivity
    echo '<div class="section">';
    echo '<h2>3. SMTP Port Connectivity Test</h2>';
    
    $commonHosts = [
        'smtp.gmail.com' => [587 => 'TLS', 465 => 'SSL'],
        'smtp.office365.com' => [587 => 'TLS'],
        'smtp.mail.yahoo.com' => [587 => 'TLS', 465 => 'SSL'],
    ];
    
    echo '<table>';
    echo '<tr><th>Host</th><th>Port</th><th>Encryption</th><th>Status</th></tr>';
    
    foreach ($commonHosts as $host => $ports) {
        foreach ($ports as $port => $encryption) {
            $socket = @stream_socket_client(
                "$host:$port",
                $errno,
                $errstr,
                5,
                STREAM_CLIENT_CONNECT,
                stream_context_create(['socket' => ['connect_timeout' => 5]])
            );
            
            if ($socket) {
                echo '<tr><td>' . $host . '</td><td>' . $port . '</td><td>' . $encryption . '</td><td><span class="success">✅ Accessible</span></td></tr>';
                fclose($socket);
            } else {
                echo '<tr><td>' . $host . '</td><td>' . $port . '</td><td>' . $encryption . '</td><td><span class="error">❌ Blocked: ' . $errstr . ' (' . $errno . ')</span></td></tr>';
            }
        }
    }
    
    echo '</table>';
    echo '<div class="info">If all ports show as blocked, your hosting provider may be blocking outbound SMTP connections.</div>';
    echo '</div>';

    // Test 4: Database SMTP Settings
    echo '<div class="section">';
    echo '<h2>4. Database SMTP Settings</h2>';
    
    try {
        require_once __DIR__ . '/config/database.php';
        $pdo = getDBConnection();
        
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'smtp_%'");
        $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($settings)) {
            echo '<div class="warning">No SMTP settings found in database. Configure SMTP in admin panel.</div>';
        } else {
            echo '<table>';
            echo '<tr><th>Setting</th><th>Value</th></tr>';
            
            foreach ($settings as $setting) {
                $key = $setting['setting_key'];
                $value = $setting['setting_value'];
                
                // Mask password
                if ($key === 'smtp_password') {
                    $value = str_repeat('*', min(strlen($value), 16)) . ' (hidden)';
                }
                
                echo '<tr><td>' . htmlspecialchars($key) . '</td><td>' . htmlspecialchars($value) . '</td></tr>';
            }
            
            echo '</table>';
            
            // Check if required settings are present
            $required = ['smtp_host', 'smtp_email', 'smtp_password'];
            $settingsKeys = array_column($settings, 'setting_key');
            $missing = array_diff($required, $settingsKeys);
            
            if (!empty($missing)) {
                echo '<div class="error"><strong>Missing required settings:</strong> ' . implode(', ', $missing) . '</div>';
            } else {
                echo '<div class="success">✅ All required SMTP settings are configured</div>';
            }
        }
    } catch (Exception $e) {
        echo '<div class="error">Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    
    echo '</div>';

    // Test 5: Manual SMTP Test
    echo '<div class="section">';
    echo '<h2>5. Manual SMTP Connection Test</h2>';
    
    if (isset($_POST['test_smtp'])) {
        $host = $_POST['smtp_host'] ?? '';
        $port = intval($_POST['smtp_port'] ?? 587);
        $encryption = $_POST['smtp_encryption'] ?? 'tls';
        $username = $_POST['smtp_username'] ?? '';
        $password = $_POST['smtp_password'] ?? '';
        $testEmail = $_POST['test_email'] ?? '';
        
        echo '<div class="test-form">';
        echo '<h3>Test Results:</h3>';
        
        if (empty($host) || empty($username) || empty($password) || empty($testEmail)) {
            echo '<div class="error">Please fill in all fields</div>';
        } else {
            // Try to load SMTP function
            try {
                require_once __DIR__ . '/config/smtp.php';
                
                $testMessage = '<html><body><h2>SMTP Diagnostic Test</h2><p>This is a test email from the SMTP diagnostic tool.</p><p>If you received this email, your SMTP configuration is working correctly!</p></body></html>';
                
                $result = sendSMTPEmail($testEmail, 'SMTP Diagnostic Test', $testMessage, $username);
                
                if ($result['success']) {
                    echo '<div class="success"><strong>✅ SUCCESS!</strong> Email sent successfully to ' . htmlspecialchars($testEmail) . '</div>';
                } else {
                    echo '<div class="error"><strong>❌ FAILED:</strong> ' . htmlspecialchars($result['error'] ?? 'Unknown error') . '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="error"><strong>❌ ERROR:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
        
        echo '</div>';
    }
    
    echo '<form method="POST">';
    echo '<div class="test-form">';
    echo '<h3>Test SMTP Connection</h3>';
    echo '<label>SMTP Host:</label>';
    echo '<input type="text" name="smtp_host" placeholder="smtp.gmail.com" value="' . htmlspecialchars($_POST['smtp_host'] ?? '') . '">';
    
    echo '<label>SMTP Port:</label>';
    echo '<select name="smtp_port">';
    echo '<option value="587"' . (($_POST['smtp_port'] ?? '587') == '587' ? ' selected' : '') . '>587 (TLS)</option>';
    echo '<option value="465"' . (($_POST['smtp_port'] ?? '') == '465' ? ' selected' : '') . '>465 (SSL)</option>';
    echo '<option value="25"' . (($_POST['smtp_port'] ?? '') == '25' ? ' selected' : '') . '>25 (Unencrypted - usually blocked)</option>';
    echo '</select>';
    
    echo '<label>Encryption:</label>';
    echo '<select name="smtp_encryption">';
    echo '<option value="tls"' . (($_POST['smtp_encryption'] ?? 'tls') == 'tls' ? ' selected' : '') . '>TLS</option>';
    echo '<option value="ssl"' . (($_POST['smtp_encryption'] ?? '') == 'ssl' ? ' selected' : '') . '>SSL</option>';
    echo '</select>';
    
    echo '<label>SMTP Username/Email:</label>';
    echo '<input type="text" name="smtp_username" placeholder="your-email@gmail.com" value="' . htmlspecialchars($_POST['smtp_username'] ?? '') . '">';
    
    echo '<label>SMTP Password (App Password for Gmail):</label>';
    echo '<input type="password" name="smtp_password" placeholder="Your SMTP password">';
    
    echo '<label>Test Email Address (where to send test email):</label>';
    echo '<input type="email" name="test_email" placeholder="test@example.com" value="' . htmlspecialchars($_POST['test_email'] ?? '') . '">';
    
    echo '<button type="submit" name="test_smtp">🧪 Test SMTP Connection</button>';
    echo '</div>';
    echo '</form>';
    
    echo '</div>';

    // Test 6: Server Information
    echo '<div class="section">';
    echo '<h2>6. Server Information</h2>';
    
    echo '<table>';
    echo '<tr><th>Item</th><th>Value</th></tr>';
    echo '<tr><td>Server Software</td><td>' . htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . '</td></tr>';
    echo '<tr><td>PHP Version</td><td>' . phpversion() . '</td></tr>';
    echo '<tr><td>Server IP</td><td>' . htmlspecialchars($_SERVER['SERVER_ADDR'] ?? 'Unknown') . '</td></tr>';
    echo '<tr><td>Document Root</td><td>' . htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . '</td></tr>';
    echo '<tr><td>Script Path</td><td>' . htmlspecialchars(__FILE__) . '</td></tr>';
    echo '</table>';
    echo '</div>';

    // Instructions
    echo '<div class="section">';
    echo '<h2>📋 Next Steps</h2>';
    echo '<ol>';
    echo '<li>Review all test results above</li>';
    echo '<li>If OpenSSL is disabled, enable it in php.ini and restart server</li>';
    echo '<li>If ports are blocked, contact your hosting provider</li>';
    echo '<li>Use the manual SMTP test to verify your credentials</li>';
    echo '<li><strong>DELETE THIS FILE</strong> after testing for security!</li>';
    echo '</ol>';
    echo '</div>';
    ?>

    <div class="section">
        <h2>🔒 Security Reminder</h2>
        <div class="warning">
            <strong>⚠️ IMPORTANT:</strong> This diagnostic tool can expose sensitive information. 
            Please delete this file (<code><?php echo htmlspecialchars(__FILE__); ?></code>) immediately after testing!
        </div>
    </div>

</body>
</html>







