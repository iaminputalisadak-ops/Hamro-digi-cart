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

// Handle preflight requests (skip in CLI)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include database config
require_once __DIR__ . '/database.php';

// Optional local secret (gitignored). Production should use env var HAMRODIGICART_APP_KEY.
@include_once __DIR__ . '/secret.php';

/**
 * Get application key for encrypting sensitive settings (e.g., SMTP password).
 */
function getAppKey() {
    $envKey = getenv('HAMRODIGICART_APP_KEY');
    if ($envKey && is_string($envKey) && trim($envKey) !== '') {
        return trim($envKey);
    }
    if (defined('HAMRODIGICART_APP_KEY') && is_string(HAMRODIGICART_APP_KEY) && trim(HAMRODIGICART_APP_KEY) !== '') {
        return trim(HAMRODIGICART_APP_KEY);
    }
    return null;
}

/**
 * Encrypt a sensitive value for storage in DB.
 * Format: enc:v1:<base64(iv|tag|ciphertext)>
 */
function encryptSensitiveValue($plaintext) {
    if (!is_string($plaintext) || $plaintext === '') return '';
    if (!extension_loaded('openssl')) {
        throw new Exception('OpenSSL extension is required to encrypt secrets');
    }
    $appKey = getAppKey();
    if (!$appKey || $appKey === 'CHANGE_ME_TO_A_LONG_RANDOM_SECRET') {
        throw new Exception('App key not configured. Set HAMRODIGICART_APP_KEY or update backend/config/secret.php');
    }
    $key = hash('sha256', $appKey, true); // 32 bytes
    $iv = random_bytes(12); // GCM recommended 12 bytes
    $tag = '';
    $cipher = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($cipher === false) {
        throw new Exception('Failed to encrypt secret');
    }
    return 'enc:v1:' . base64_encode($iv . $tag . $cipher);
}

/**
 * Decrypt a sensitive value from DB.
 */
function decryptSensitiveValue($value) {
    if (!is_string($value) || $value === '') return '';
    if (strpos($value, 'enc:v1:') !== 0) {
        // Backward compatibility: stored in plaintext
        return $value;
    }
    if (!extension_loaded('openssl')) {
        throw new Exception('OpenSSL extension is required to decrypt secrets');
    }
    $appKey = getAppKey();
    if (!$appKey || $appKey === 'CHANGE_ME_TO_A_LONG_RANDOM_SECRET') {
        throw new Exception('App key not configured. Set HAMRODIGICART_APP_KEY or update backend/config/secret.php');
    }
    $key = hash('sha256', $appKey, true);
    $raw = base64_decode(substr($value, strlen('enc:v1:')), true);
    if ($raw === false || strlen($raw) < 12 + 16 + 1) {
        throw new Exception('Invalid encrypted secret format');
    }
    $iv = substr($raw, 0, 12);
    $tag = substr($raw, 12, 16);
    $cipher = substr($raw, 28);
    $plain = openssl_decrypt($cipher, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($plain === false) {
        throw new Exception('Failed to decrypt secret');
    }
    return $plain;
}

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
 * Get favicon URL from settings
 */
function getFaviconUrl() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'website_favicon'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && !empty($result['setting_value'])) {
            return htmlspecialchars(trim($result['setting_value']));
        }
    } catch (Exception $e) {
        // If database query fails, return empty
    }
    return '';
}

/**
 * Get website logo URL from settings
 */
function getWebsiteLogoUrl() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'website_logo'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && !empty($result['setting_value'])) {
            return htmlspecialchars(trim($result['setting_value']));
        }
    } catch (Exception $e) {
        // If database query fails, return empty
    }
    return '';
}

/**
 * Upload file
 */
function uploadFile($file, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/x-icon', 'image/vnd.microsoft.icon'], $maxSize = 5242880) {
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
            'webp' => 'image/webp',
            'ico' => 'image/x-icon'
        ];
        $mimeType = isset($extensionMap[$extension]) ? $extensionMap[$extension] : '';
    }

    if (empty($mimeType) || !in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: JPEG, PNG, GIF, WebP, ICO'];
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
        // Compute a web-accessible uploads path that works both locally and on cPanel.
        // - Local dev (backend served at /): /uploads/<file>
        // - cPanel typical (backend in /backend): /backend/uploads/<file>
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = '';
        if (!empty($scriptName)) {
            // Example: /backend/api/upload.php -> /backend
            // Example: /api/upload.php -> /
            $basePath = dirname($scriptName, 2);
            $basePath = str_replace('\\', '/', $basePath);
            if ($basePath === '/' || $basePath === '.' || $basePath === '\\') {
                $basePath = '';
            }
            $basePath = rtrim($basePath, '/');
        }

        return ['success' => true, 'filename' => $filename, 'path' => $basePath . '/uploads/' . $filename];
    }

    return ['success' => false, 'error' => 'Failed to move uploaded file'];
}

