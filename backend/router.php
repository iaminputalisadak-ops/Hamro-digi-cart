<?php
/**
 * Router for PHP Built-in Server
 * Handles uploads directory access
 */

// Get the request URI
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'] ?? '/';

// Log for debugging (can be removed in production)
// error_log("Router called for: $path");

// Serve uploaded files
if (preg_match('#^/uploads/(.+)$#', $path, $matches)) {
    $filename = $matches[1];
    // Decode URL-encoded filename
    $filename = urldecode($filename);
    
    // Remove any path traversal attempts - only allow filename
    $filename = basename($filename);
    
    // Use DIRECTORY_SEPARATOR for cross-platform compatibility
    $uploadsDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
    $filePath = $uploadsDir . $filename;
    
    // Normalize path separators
    $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
    $uploadsDir = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $uploadsDir);
    
    // Check if file exists
    if (file_exists($filePath) && is_file($filePath)) {
        // Security: Ensure file is within uploads directory
        $realFilePath = realpath($filePath);
        $realUploadsDir = realpath($uploadsDir);
        
        $isSecure = false;
        if ($realFilePath && $realUploadsDir) {
            // Use realpath for security check (case-insensitive on Windows)
            $isSecure = stripos($realFilePath, $realUploadsDir) === 0;
        } else {
            // Fallback: check if normalized path starts with uploads directory
            $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
            $normalizedDir = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $uploadsDir);
            $isSecure = stripos($normalizedPath, $normalizedDir) === 0;
        }
        
        if ($isSecure) {
            // Use the actual file path
            $actualFilePath = $realFilePath ?: $filePath;
            
            // Determine MIME type
            $mimeType = 'application/octet-stream'; // Default
            $extension = strtolower(pathinfo($actualFilePath, PATHINFO_EXTENSION));
            
            // Try to get MIME type using fileinfo if available
            if (function_exists('mime_content_type')) {
                $mimeType = @mime_content_type($actualFilePath);
            } elseif (function_exists('finfo_open')) {
                $finfo = @finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $mimeType = @finfo_file($finfo, $actualFilePath);
                    @finfo_close($finfo);
                }
            }
            
            // Fallback to extension-based MIME type detection
            if (!$mimeType || $mimeType === 'application/octet-stream') {
                $mimeTypes = [
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp'
                ];
                $mimeType = isset($mimeTypes[$extension]) ? $mimeTypes[$extension] : 'application/octet-stream';
            }
            
            // Set headers
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . filesize($actualFilePath));
            header('Cache-Control: public, max-age=31536000');
            
            // Output file
            readfile($actualFilePath);
            exit;
        }
    }
    
    // File not found or security check failed
    http_response_code(404);
    header('Content-Type: text/plain');
    header('Access-Control-Allow-Origin: *');
    echo "File not found";
    exit;
}

// Handle OPTIONS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    http_response_code(200);
    exit;
}

// Let PHP handle other requests normally
return false;

