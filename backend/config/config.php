<?php
/**
 * Application Configuration
 */

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Kathmandu');

// Error reporting (disable in production)
// For API endpoints, errors should be in JSON, not HTML
error_reporting(E_ALL);
// Only display errors for non-API pages
if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === false) {
    ini_set('display_errors', 1);
} else {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// CORS headers (if needed for API)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include database config
require_once __DIR__ . '/database.php';

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

/**
 * Require admin login
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            sendError('Unauthorized', 401);
        } else {
            header('Location: /admin/login.php');
            exit;
        }
    }
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Upload file
 */
function uploadFile($file, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], $maxSize = 5242880) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'No file uploaded'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        $errorMsg = isset($errorMessages[$file['error']]) ? $errorMessages[$file['error']] : 'Upload error';
        return ['success' => false, 'error' => $errorMsg];
    }

    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File size exceeds limit (max ' . ($maxSize / 1024 / 1024) . 'MB)'];
    }

    // Check MIME type
    $mimeType = '';
    if (function_exists('finfo_open')) {
        $finfo = @finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mimeType = @finfo_file($finfo, $file['tmp_name']);
            @finfo_close($finfo);
        }
    }
    
    // Fallback to file extension if finfo is not available
    if (empty($mimeType)) {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extensionMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        $mimeType = isset($extensionMap[$extension]) ? $extensionMap[$extension] : '';
    }

    if (empty($mimeType) || !in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: JPEG, PNG, GIF, WebP'];
    }

    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        if (!@mkdir($uploadDir, 0755, true)) {
            return ['success' => false, 'error' => 'Failed to create upload directory'];
        }
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;

    if (@move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'path' => '/uploads/' . $filename];
    }

    return ['success' => false, 'error' => 'Failed to move uploaded file'];
}

