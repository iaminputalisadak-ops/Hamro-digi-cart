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
        // Get the base URL
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $protocol . '://' . $host;
        
        // Return full URL for the uploaded file
        $fullUrl = $baseUrl . $result['path'];
        
        ob_end_clean();
        sendSuccess([
            'filename' => $result['filename'],
            'url' => $fullUrl,
            'path' => $result['path']
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

