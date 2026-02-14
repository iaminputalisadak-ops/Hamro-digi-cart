<?php
/**
 * Contact Form API
 * Handles contact form submissions from the frontend
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/smtp.php';

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Suppress error display for clean JSON output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

ob_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    sendError('Method not allowed', 405);
    exit;
}

ob_clean();

$input = file_get_contents('php://input');

if (empty($input)) {
    ob_end_clean();
    sendError('No data received', 400);
    exit;
}

$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    ob_end_clean();
    sendError('Invalid JSON data: ' . json_last_error_msg(), 400);
    exit;
}

// Validate required fields
if (!isset($data['name']) || empty(trim($data['name']))) {
    ob_end_clean();
    sendError('Name is required', 400);
    exit;
}

if (!isset($data['email']) || empty(trim($data['email']))) {
    ob_end_clean();
    sendError('Email is required', 400);
    exit;
}

if (!isset($data['subject']) || empty(trim($data['subject']))) {
    ob_end_clean();
    sendError('Subject is required', 400);
    exit;
}

if (!isset($data['message']) || empty(trim($data['message']))) {
    ob_end_clean();
    sendError('Message is required', 400);
    exit;
}

$name = trim($data['name']);
$email = trim($data['email']);
$subject = trim($data['subject']);
$message = trim($data['message']);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ob_end_clean();
    sendError('Invalid email address format', 400);
    exit;
}

// Get SMTP email from settings (this is where contact form messages will be sent)
try {
    $pdo = getDBConnection();
    
    // Try to get SMTP email from settings
    $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'smtp_email'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $adminEmail = $result ? trim($result['setting_value']) : '';
    
    if (empty($adminEmail)) {
        ob_end_clean();
        sendError('Contact form is not configured. Please configure SMTP email in admin settings.', 500);
        exit;
    }
    
    // Validate admin email format
    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        ob_end_clean();
        sendError('Admin email is not properly configured. Please check SMTP settings.', 500);
        exit;
    }
    
} catch (Exception $e) {
    ob_end_clean();
    sendError('Error getting email configuration: ' . $e->getMessage(), 500);
    exit;
}

// Check SMTP configuration before attempting to send
try {
    $smtpSettings = getSMTPSettings();
    
    $missingSettings = [];
    if (empty($smtpSettings['smtp_host'])) {
        $missingSettings[] = 'SMTP Host';
    }
    if (empty($smtpSettings['smtp_email'])) {
        $missingSettings[] = 'SMTP Email';
    }
    if (empty($smtpSettings['smtp_password'])) {
        $missingSettings[] = 'SMTP Password';
    }
    
    if (!empty($missingSettings)) {
        ob_end_clean();
        $errorMsg = 'SMTP not configured. Missing: ' . implode(', ', $missingSettings) . '. ';
        $errorMsg .= 'Please go to Admin Panel → Settings → SMTP Email Settings and configure all required fields.';
        sendError($errorMsg, 500);
        exit;
    }
} catch (Exception $e) {
    ob_end_clean();
    sendError('Error checking SMTP configuration: ' . $e->getMessage(), 500);
    exit;
}

// Prepare email content
$emailSubject = "Contact Form: " . htmlspecialchars($subject);
$emailMessage = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #667eea; color: white; padding: 20px; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-top: none; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #667eea; }
        .value { margin-top: 5px; padding: 10px; background-color: white; border-radius: 4px; }
        .message { white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>New Contact Form Submission</h2>
        </div>
        <div class='content'>
            <div class='field'>
                <div class='label'>Name:</div>
                <div class='value'>" . htmlspecialchars($name) . "</div>
            </div>
            <div class='field'>
                <div class='label'>Email:</div>
                <div class='value'>" . htmlspecialchars($email) . "</div>
            </div>
            <div class='field'>
                <div class='label'>Subject:</div>
                <div class='value'>" . htmlspecialchars($subject) . "</div>
            </div>
            <div class='field'>
                <div class='label'>Message:</div>
                <div class='value message'>" . nl2br(htmlspecialchars($message)) . "</div>
            </div>
        </div>
    </div>
</body>
</html>
";

// Send email using SMTP
try {
    $result = sendSMTPEmail($adminEmail, $emailSubject, $emailMessage, null, 'Hamro Digi Cart Contact Form');
    
    if ($result['success']) {
        ob_end_clean();
        sendSuccess([
            'sent' => true,
            'message' => 'Your message has been sent successfully. We will get back to you soon!'
        ], 'Message sent successfully');
    } else {
        ob_end_clean();
        $errorMsg = isset($result['error']) ? $result['error'] : 'Failed to send message';
        sendError($errorMsg, 500);
    }
    
} catch (Exception $e) {
    ob_end_clean();
    sendError('Error sending message: ' . $e->getMessage(), 500);
}









