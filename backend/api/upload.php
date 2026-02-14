<?php
/**
 * File Upload API
 */

// Suppress error display for clean JSON output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../config/config.php';

// Ensure we output JSON only
ob_start();

// Check authentication directly to avoid any output
if (!isAdminLoggedIn()) {
    ob_end_clean();
    sendError('Authentication required', 401);
    exit;
}

// Clean any output before processing
ob_clean();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    sendError('Method not allowed', 405);
    exit;
}

if (!isset($_FILES['file'])) {
    ob_end_clean();
    sendError('No file uploaded');
    exit;
}

try {
    $result = uploadFile($_FILES['file']);
    
    if ($result['success']) {
        // Prefer returning a root-relative URL to avoid cPanel/proxy scheme issues (http vs https).
        // The frontend/admin can resolve this against window.location.origin.
        $relativeUrl = $result['path'];

        // Best-effort absolute URL (kept for backward compatibility / debugging)
        $scheme = 'http';
        if (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) ||
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
        ) {
            $scheme = 'https';
        }
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $absoluteUrl = ($host ? ($scheme . '://' . $host . $relativeUrl) : $relativeUrl);
        
        ob_end_clean();
        sendSuccess([
            'filename' => $result['filename'],
            // Keep existing key name expected by admin.js: now root-relative for portability
            'url' => $relativeUrl,
            'path' => $relativeUrl,
            'absolute_url' => $absoluteUrl
        ], 'File uploaded successfully');
    } else {
        ob_end_clean();
        sendError($result['error'] || 'Upload failed');
    }
} catch (Exception $e) {
    ob_end_clean();
    sendError('Upload error: ' . $e->getMessage());
} catch (Error $e) {
    ob_end_clean();
    sendError('Upload error: ' . $e->getMessage());
}

