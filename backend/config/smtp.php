<?php
/**
 * SMTP Configuration Helper
 * Gets SMTP settings from database
 */

require_once __DIR__ . '/database.php';

/**
 * Get SMTP settings from database
 */
function getSMTPSettings() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'smtp_%'");
    $settings = $stmt->fetchAll();
    
    $smtpSettings = [];
    foreach ($settings as $setting) {
        $smtpSettings[$setting['setting_key']] = $setting['setting_value'];
    }

    // Decrypt smtp_password if stored encrypted
    if (isset($smtpSettings['smtp_password']) && is_string($smtpSettings['smtp_password']) && $smtpSettings['smtp_password'] !== '') {
        try {
            $rawValue = $smtpSettings['smtp_password'];
            $smtpSettings['smtp_password'] = decryptSensitiveValue($rawValue);

            // If it was plaintext (backward compatibility), migrate to encrypted storage.
            if (strpos($rawValue, 'enc:v1:') !== 0) {
                try {
                    $encrypted = encryptSensitiveValue($rawValue);
                    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = CURRENT_TIMESTAMP");
                    $stmt->execute(['smtp_password', $encrypted, $encrypted]);
                } catch (Throwable $e) {
                    // ignore migration failure
                }
            }
        } catch (Throwable $e) {
            // Don't hard-fail the entire settings fetch; log and keep empty
            error_log('Failed to decrypt smtp_password: ' . $e->getMessage());
            $smtpSettings['smtp_password'] = '';
        }
    }
    
    return $smtpSettings;
}

/**
 * Send email using SMTP
 */
function sendSMTPEmail($to, $subject, $message, $fromEmail = null, $fromName = null) {
    $settings = getSMTPSettings();
    
    // Check if SMTP is configured
    if (empty($settings['smtp_host']) || empty($settings['smtp_email']) || empty($settings['smtp_password'])) {
        return ['success' => false, 'error' => 'SMTP not configured. Please configure SMTP settings in admin panel.'];
    }
    
    $host = $settings['smtp_host'];
    $port = $settings['smtp_port'] ?? 587;
    $encryption = $settings['smtp_encryption'] ?? 'tls';
    $username = $settings['smtp_email'];
    $password = $settings['smtp_password'];
    $from = $fromEmail ?? $username;
    $fromName = $fromName ?? ($settings['smtp_from_name'] ?? 'Hamro Digi Cart');
    
    // Try with configured settings first
    $result = sendSMTPViaSocket($host, $port, $encryption, $username, $password, $from, $fromName, $to, $subject, $message);
    
    // If TLS fails and we're using TLS, automatically try SSL as fallback (for live server compatibility)
    if (!$result['success'] && ($encryption === 'tls' || $encryption === 'TLS') && 
        (strpos($result['error'], 'Connection failed') !== false || 
         strpos($result['error'], 'Connection refused') !== false ||
         strpos($result['error'], 'timed out') !== false ||
         strpos($result['error'], 'Connection timed out') !== false ||
         strpos($result['error'], '110') !== false ||
         strpos($result['error'], 'TLS') !== false ||
         strpos($result['error'], 'timeout') !== false)) {
        
        // Try SSL on port 465 as automatic fallback
        $fallbackResult = sendSMTPViaSocket($host, 465, 'ssl', $username, $password, $from, $fromName, $to, $subject, $message);
        
        if ($fallbackResult['success']) {
            // SSL worked! Update settings in database for future use
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute(['smtp_port', '465', '465']);
                $stmt->execute(['smtp_encryption', 'ssl', 'ssl']);
            } catch (Exception $e) {
                // Ignore database update errors, email was sent successfully
            }
            return ['success' => true, 'message' => 'Email sent successfully via SSL (port 465). Your settings have been automatically updated to use SSL instead of TLS.'];
        } else {
            // Both TLS and SSL failed, return original error with helpful message
            $originalError = $result['error'];
            $fallbackError = $fallbackResult['error'];
            return [
                'success' => false, 
                'error' => "TLS (port 587) failed: " . $originalError . " SSL fallback (port 465) also failed: " . $fallbackError . " Both ports may be blocked. Contact your hosting provider or use a third-party SMTP service."
            ];
        }
    }
    
    return $result;
}

/**
 * Send email via SMTP using sockets
 */
function sendSMTPViaSocket($host, $port, $encryption, $username, $password, $from, $fromName, $to, $subject, $message) {
    $socket = null;
    $lastError = null;
    
    try {
        // Remove spaces from password (Gmail App Passwords are displayed with spaces but should be used without)
        $password = str_replace(' ', '', trim($password));
        
        // Trim all inputs
        $host = trim($host);
        $username = trim($username);
        $from = trim($from);
        $to = trim($to);
        
        // Validate inputs
        if (empty($host) || empty($username) || empty($password) || empty($to)) {
            return ['success' => false, 'error' => 'Missing required SMTP parameters'];
        }
        
        // Create socket connection with improved SSL context
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT,
                'disable_compression' => true
            ],
            'socket' => [
                'tcp_nodelay' => true
            ]
        ]);
        
        // For TLS, connect without SSL first, then upgrade
        if ($encryption === 'tls' || $encryption === 'TLS') {
            $socket = @stream_socket_client(
                $host . ':' . $port,
                $errno,
                $errstr,
                60, // Increased timeout for live servers
                STREAM_CLIENT_CONNECT,
                $context
            );
            
            // If TLS fails, try SSL as fallback (for live server compatibility)
            if (!$socket && ($errno === 110 || $errno === 111 || strpos($errstr, 'Connection refused') !== false || strpos($errstr, 'timed out') !== false)) {
                $lastError = "TLS connection failed: $errstr ($errno). Trying SSL fallback...";
                // Try SSL on port 465 as fallback
                $transports = stream_get_transports();
                if (in_array('ssl', $transports)) {
                    $socket = @stream_socket_client(
                        'ssl://' . $host . ':465',
                        $errno,
                        $errstr,
                        60,
                        STREAM_CLIENT_CONNECT,
                        $context
                    );
                    if ($socket) {
                        $encryption = 'ssl';
                        $port = 465;
                    }
                }
            }
        } else {
            // For SSL, check if SSL transport is available
            $transports = stream_get_transports();
            if (!in_array('ssl', $transports)) {
                return ['success' => false, 'error' => 'SSL transport is not available. OpenSSL extension must be enabled in php.ini. Please enable extension=openssl in your PHP configuration file and restart the server.'];
            }
            
            // For SSL, connect with SSL wrapper
            $socket = @stream_socket_client(
                'ssl://' . $host . ':' . $port,
                $errno,
                $errstr,
                60, // Increased timeout for live servers
                STREAM_CLIENT_CONNECT,
                $context
            );
        }
        
        if (!$socket) {
            $errorMsg = "Connection failed: $errstr ($errno). ";
            if ($lastError) {
                $errorMsg = $lastError . " " . $errorMsg;
            }
            if ($encryption === 'ssl' && strpos($errstr, 'ssl') !== false) {
                $errorMsg .= 'SSL transport not available. Please enable OpenSSL extension in php.ini (extension=openssl) and restart your PHP server.';
            } else if (strpos($errstr, 'Connection refused') !== false || $errno === 111) {
                $errorMsg .= "Port $port may be blocked by firewall. Try port 465 (SSL) or contact your hosting provider to unblock SMTP ports.";
            } else if (strpos($errstr, 'timed out') !== false || $errno === 110 || strpos($errstr, 'Connection timed out') !== false) {
                $errorMsg .= "Connection timeout (Error $errno). Port $port may be blocked by your hosting provider's firewall. ";
                if ($port == 587) {
                    $errorMsg .= "Try switching to SSL (port 465) in your SMTP settings. The system will automatically try this as a fallback, but you can also manually change: Port to 465 and Encryption to SSL.";
                } else {
                    $errorMsg .= "Contact your hosting provider to unblock SMTP ports, or use a third-party SMTP service like SendGrid or Mailgun.";
                }
            } else {
                $errorMsg .= "Check host ($host) and port ($port). If using Gmail, ensure you're using an App Password (not regular password).";
            }
            return ['success' => false, 'error' => $errorMsg];
        }
        
        // Set timeout for socket operations (increased for live servers)
        stream_set_timeout($socket, 60);
        
        // Get server hostname for EHLO (use actual hostname or IP)
        $serverHostname = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : (gethostname() ?: $host);
        
        // Read server greeting
        $greeting = fgets($socket, 515);
        if (!$greeting) {
            $meta = stream_get_meta_data($socket);
            fclose($socket);
            if ($meta['timed_out']) {
                return ['success' => false, 'error' => 'Connection timeout while waiting for server greeting'];
            }
            return ['success' => false, 'error' => 'Failed to receive server greeting'];
        }
        
        // Send EHLO with proper hostname
        fputs($socket, "EHLO $serverHostname\r\n");
        $response = '';
        $timeout = 0;
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            $meta = stream_get_meta_data($socket);
            if ($meta['timed_out']) {
                fclose($socket);
                return ['success' => false, 'error' => 'Connection timeout while reading EHLO response'];
            }
            if (strlen($line) > 3 && substr($line, 3, 1) == ' ') break;
        }
        
        // Start TLS if needed
        if ($encryption === 'tls') {
            if (strpos($response, 'STARTTLS') === false) {
                fclose($socket);
                return ['success' => false, 'error' => 'Server does not support STARTTLS'];
            }
            
            fputs($socket, "STARTTLS\r\n");
            $tlsResponse = fgets($socket, 515);
            if (!$tlsResponse || strpos($tlsResponse, '220') === false) {
                fclose($socket);
                return ['success' => false, 'error' => 'TLS negotiation failed: ' . trim($tlsResponse)];
            }
            
            // Check if OpenSSL is available
            if (!extension_loaded('openssl')) {
                fclose($socket);
                return ['success' => false, 'error' => 'OpenSSL extension is not enabled. Please restart your PHP server after enabling extension=openssl in php.ini. Alternatively, use SSL (port 465) instead of TLS (port 587) in your SMTP settings.'];
            }
            
            // Try different TLS methods if the default one fails
            $cryptoMethods = [
                STREAM_CRYPTO_METHOD_TLS_CLIENT,
                STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT,
                STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT
            ];
            
            $cryptoEnabled = false;
            $lastError = '';
            foreach ($cryptoMethods as $method) {
                $result = @stream_socket_enable_crypto($socket, true, $method);
                if ($result === true) {
                    $cryptoEnabled = true;
                    break;
                }
                $lastError = error_get_last();
            }
            
            if (!$cryptoEnabled) {
                fclose($socket);
                $errorMsg = 'Failed to enable TLS encryption. ';
                $errorMsg .= 'Try using SSL (port 465) instead of TLS (port 587) in your SMTP settings. ';
                $errorMsg .= 'Or restart your PHP server after enabling OpenSSL extension.';
                return ['success' => false, 'error' => $errorMsg];
            }
            
            // Send EHLO again after TLS
            fputs($socket, "EHLO $serverHostname\r\n");
            $response = '';
            while ($line = fgets($socket, 515)) {
                $response .= $line;
                if (strlen($line) > 3 && substr($line, 3, 1) == ' ') break;
            }
        }
        
        // Authenticate
        fputs($socket, "AUTH LOGIN\r\n");
        $authInit = fgets($socket, 515);
        if (!$authInit) {
            $meta = stream_get_meta_data($socket);
            fclose($socket);
            if ($meta['timed_out']) {
                return ['success' => false, 'error' => 'Connection timeout during authentication'];
            }
            return ['success' => false, 'error' => 'No response from server for AUTH LOGIN'];
        }
        
        if (strpos($authInit, '334') === false && strpos($authInit, '250') === false) {
            fclose($socket);
            return ['success' => false, 'error' => 'AUTH LOGIN not accepted: ' . trim($authInit)];
        }
        
        // Send username
        fputs($socket, base64_encode($username) . "\r\n");
        $userResponse = fgets($socket, 515);
        if (!$userResponse) {
            $meta = stream_get_meta_data($socket);
            fclose($socket);
            if ($meta['timed_out']) {
                return ['success' => false, 'error' => 'Connection timeout while sending username'];
            }
            return ['success' => false, 'error' => 'No response from server for username'];
        }
        
        if (strpos($userResponse, '334') === false) {
            fclose($socket);
            return ['success' => false, 'error' => 'Username not accepted: ' . trim($userResponse)];
        }
        
        // Send password
        fputs($socket, base64_encode($password) . "\r\n");
        $authResponse = fgets($socket, 515);
        
        if (!$authResponse) {
            $meta = stream_get_meta_data($socket);
            fclose($socket);
            if ($meta['timed_out']) {
                return ['success' => false, 'error' => 'Connection timeout while authenticating password'];
            }
            return ['success' => false, 'error' => 'No response from server for password authentication'];
        }
        
        if (strpos($authResponse, '235') === false) {
            fclose($socket);
            $errorMsg = trim($authResponse);
            if (strpos($errorMsg, '535') !== false || strpos($errorMsg, '534') !== false) {
                return ['success' => false, 'error' => 'SMTP authentication failed: Invalid email or password. For Gmail, make sure you\'re using an App Password (not your regular password) and that 2-Step Verification is enabled. Generate App Password at: https://myaccount.google.com/apppasswords'];
            } else if (strpos($errorMsg, '454') !== false) {
                return ['success' => false, 'error' => 'SMTP authentication failed: Temporary authentication failure. Please try again in a few minutes.'];
            }
            return ['success' => false, 'error' => 'SMTP authentication failed: ' . $errorMsg];
        }
        
        // Send email
        fputs($socket, "MAIL FROM: <$from>\r\n");
        $mailFromResponse = fgets($socket, 515);
        if (!$mailFromResponse) {
            $meta = stream_get_meta_data($socket);
            fclose($socket);
            if ($meta['timed_out']) {
                return ['success' => false, 'error' => 'Connection timeout during MAIL FROM command'];
            }
            return ['success' => false, 'error' => 'No response from server for MAIL FROM'];
        }
        
        if (strpos($mailFromResponse, '250') === false) {
            fclose($socket);
            return ['success' => false, 'error' => 'MAIL FROM failed: ' . trim($mailFromResponse)];
        }
        
        fputs($socket, "RCPT TO: <$to>\r\n");
        $rcptToResponse = fgets($socket, 515);
        if (!$rcptToResponse) {
            $meta = stream_get_meta_data($socket);
            fclose($socket);
            if ($meta['timed_out']) {
                return ['success' => false, 'error' => 'Connection timeout during RCPT TO command'];
            }
            return ['success' => false, 'error' => 'No response from server for RCPT TO'];
        }
        
        if (strpos($rcptToResponse, '250') === false) {
            fclose($socket);
            return ['success' => false, 'error' => 'RCPT TO failed: ' . trim($rcptToResponse)];
        }
        
        fputs($socket, "DATA\r\n");
        $dataResponse = fgets($socket, 515);
        if (!$dataResponse) {
            $meta = stream_get_meta_data($socket);
            fclose($socket);
            if ($meta['timed_out']) {
                return ['success' => false, 'error' => 'Connection timeout during DATA command'];
            }
            return ['success' => false, 'error' => 'No response from server for DATA command'];
        }
        
        if (strpos($dataResponse, '354') === false) {
            fclose($socket);
            return ['success' => false, 'error' => 'DATA command failed: ' . trim($dataResponse)];
        }
        
        // Email headers
        $headers = "From: $fromName <$from>\r\n";
        $headers .= "To: <$to>\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "\r\n";
        
        // Escape dots at the start of lines in message body (RFC 5321)
        $escapedMessage = preg_replace('/^\./m', '..', $message);
        
        fputs($socket, $headers . $escapedMessage . "\r\n.\r\n");
        
        // Wait for response with timeout handling
        $dataEndResponse = '';
        $startTime = time();
        while (true) {
            $line = fgets($socket, 515);
            if ($line !== false) {
                $dataEndResponse .= $line;
                if (strlen($line) > 3 && substr($line, 3, 1) == ' ') {
                    break; // End of multi-line response
                }
            } else {
                $meta = stream_get_meta_data($socket);
                if ($meta['timed_out'] || (time() - $startTime) > 30) {
                    fclose($socket);
                    return ['success' => false, 'error' => 'Connection timeout while waiting for email send confirmation'];
                }
                usleep(100000); // Wait 100ms before checking again
            }
        }
        
        if (empty($dataEndResponse) || strpos($dataEndResponse, '250') === false) {
            fclose($socket);
            $errorMsg = trim($dataEndResponse);
            if (empty($errorMsg)) {
                return ['success' => false, 'error' => 'Email sending failed: No response from server'];
            }
            return ['success' => false, 'error' => 'Email sending failed: ' . $errorMsg];
        }
        
        // Gracefully close connection
        @fputs($socket, "QUIT\r\n");
        @fgets($socket, 515); // Read QUIT response
        fclose($socket);
        
        return ['success' => true, 'message' => 'Email sent successfully'];
        
    } catch (Exception $e) {
        if ($socket && is_resource($socket)) {
            @fclose($socket);
        }
        return ['success' => false, 'error' => 'SMTP Error: ' . $e->getMessage()];
    } catch (Error $e) {
        if ($socket && is_resource($socket)) {
            @fclose($socket);
        }
        return ['success' => false, 'error' => 'SMTP Error: ' . $e->getMessage()];
    }
}

