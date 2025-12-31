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
    
    // Use socket-based SMTP sending
    return sendSMTPViaSocket($host, $port, $encryption, $username, $password, $from, $fromName, $to, $subject, $message);
}

/**
 * Send email via SMTP using sockets
 */
function sendSMTPViaSocket($host, $port, $encryption, $username, $password, $from, $fromName, $to, $subject, $message) {
    try {
        // Remove spaces from password (Gmail App Passwords are displayed with spaces but should be used without)
        $password = str_replace(' ', '', trim($password));
        
        // Create socket connection
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        // For TLS, connect without SSL first, then upgrade
        if ($encryption === 'tls') {
            $socket = @stream_socket_client(
                $host . ':' . $port,
                $errno,
                $errstr,
                30,
                STREAM_CLIENT_CONNECT,
                $context
            );
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
                30,
                STREAM_CLIENT_CONNECT,
                $context
            );
        }
        
        if (!$socket) {
            $errorMsg = "Connection failed: $errstr ($errno). ";
            if ($encryption === 'ssl' && strpos($errstr, 'ssl') !== false) {
                $errorMsg .= 'SSL transport not available. Please enable OpenSSL extension in php.ini (extension=openssl) and restart your PHP server.';
            } else {
                $errorMsg .= "Check host ($host) and port ($port).";
            }
            return ['success' => false, 'error' => $errorMsg];
        }
        
        // Set timeout for socket operations
        stream_set_timeout($socket, 30);
        
        // Read server greeting
        $greeting = fgets($socket, 515);
        if (!$greeting) {
            fclose($socket);
            return ['success' => false, 'error' => 'Failed to receive server greeting'];
        }
        
        // Send EHLO
        fputs($socket, "EHLO $host\r\n");
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
            fputs($socket, "EHLO $host\r\n");
            $response = '';
            while ($line = fgets($socket, 515)) {
                $response .= $line;
                if (strlen($line) > 3 && substr($line, 3, 1) == ' ') break;
            }
        }
        
        // Authenticate
        fputs($socket, "AUTH LOGIN\r\n");
        $authInit = fgets($socket, 515);
        if (!$authInit || strpos($authInit, '334') === false) {
            fclose($socket);
            return ['success' => false, 'error' => 'AUTH LOGIN not accepted: ' . trim($authInit)];
        }
        
        fputs($socket, base64_encode($username) . "\r\n");
        $userResponse = fgets($socket, 515);
        if (!$userResponse || strpos($userResponse, '334') === false) {
            fclose($socket);
            return ['success' => false, 'error' => 'Username not accepted: ' . trim($userResponse)];
        }
        
        fputs($socket, base64_encode($password) . "\r\n");
        $authResponse = fgets($socket, 515);
        
        if (!$authResponse || strpos($authResponse, '235') === false) {
            fclose($socket);
            $errorMsg = trim($authResponse);
            if (strpos($errorMsg, '535') !== false) {
                return ['success' => false, 'error' => 'SMTP authentication failed: Invalid email or password. For Gmail, make sure you\'re using an App Password (not your regular password) and that 2-Step Verification is enabled.'];
            }
            return ['success' => false, 'error' => 'SMTP authentication failed: ' . $errorMsg];
        }
        
        // Send email
        fputs($socket, "MAIL FROM: <$from>\r\n");
        $mailFromResponse = fgets($socket, 515);
        if (strpos($mailFromResponse, '250') === false) {
            fclose($socket);
            return ['success' => false, 'error' => 'MAIL FROM failed: ' . trim($mailFromResponse)];
        }
        
        fputs($socket, "RCPT TO: <$to>\r\n");
        $rcptToResponse = fgets($socket, 515);
        if (strpos($rcptToResponse, '250') === false) {
            fclose($socket);
            return ['success' => false, 'error' => 'RCPT TO failed: ' . trim($rcptToResponse)];
        }
        
        fputs($socket, "DATA\r\n");
        $dataResponse = fgets($socket, 515);
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
        $dataEndResponse = fgets($socket, 515);
        if (!$dataEndResponse || strpos($dataEndResponse, '250') === false) {
            fclose($socket);
            $errorMsg = trim($dataEndResponse);
            return ['success' => false, 'error' => 'Email sending failed: ' . ($errorMsg ?: 'No response from server')];
        }
        
        fputs($socket, "QUIT\r\n");
        fclose($socket);
        
        return ['success' => true, 'message' => 'Email sent successfully'];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

