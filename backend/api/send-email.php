<?php
/**
 * Send Email API using SMTP
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/smtp.php';

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

// For test emails, allow without admin login
$isTest = isset($data['test']) && $data['test'] === true;

if (!$isTest) {
    // Check authentication before processing
    if (!isAdminLoggedIn()) {
        ob_end_clean();
        sendError('Authentication required. Please log in again.', 401);
        exit;
    }
}

if (!isset($data['to']) || empty($data['to'])) {
    ob_end_clean();
    sendError('Email address (to) is required', 400);
    exit;
}

if (!isset($data['subject']) || empty(trim($data['subject']))) {
    ob_end_clean();
    sendError('Email subject is required', 400);
    exit;
}

if (!isset($data['message']) || empty(trim($data['message']))) {
    ob_end_clean();
    sendError('Email message is required', 400);
    exit;
}

$to = trim($data['to']);
$subject = trim($data['subject']);
$message = trim($data['message']);
$fromEmail = isset($data['from']) ? trim($data['from']) : null;
$fromName = isset($data['from_name']) ? trim($data['from_name']) : null;

// Validate email format
if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
    ob_end_clean();
    sendError('Invalid email address format: ' . htmlspecialchars($to), 400);
    exit;
}

// Validate subject length
if (strlen($subject) > 200) {
    ob_end_clean();
    sendError('Email subject is too long (max 200 characters)', 400);
    exit;
}

// Validate message is not empty after trimming
if (empty($message)) {
    ob_end_clean();
    sendError('Email message cannot be empty', 400);
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
        sendError($errorMsg, 400);
        exit;
    }
} catch (Exception $e) {
    ob_end_clean();
    sendError('Error checking SMTP configuration: ' . $e->getMessage() . '. Please check database connection.', 500);
    exit;
}

// Send email using SMTP
try {
    $result = sendSMTPEmail($to, $subject, $message, $fromEmail, $fromName);
    
    if ($result['success']) {
        ob_end_clean();
        sendSuccess([
            'sent' => true,
            'to' => $to
        ], 'Email sent successfully via SMTP');
    } else {
        ob_end_clean();
        $errorMsg = isset($result['error']) ? $result['error'] : 'Failed to send email';
        sendError($errorMsg, 500);
    }
    
} catch (Exception $e) {
    ob_end_clean();
    sendError('Email error: ' . $e->getMessage(), 500);
}

