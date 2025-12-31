<?php
/**
 * Check if OpenSSL is loaded in web server context
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== PHP OpenSSL Check ===\n\n";

echo "OpenSSL Extension Loaded: " . (extension_loaded('openssl') ? 'YES ✓' : 'NO ✗') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Loaded php.ini: " . php_ini_loaded_file() . "\n";
echo "Additional ini files: " . php_ini_scanned_files() . "\n\n";

echo "Available Stream Transports:\n";
$transports = stream_get_transports();
foreach ($transports as $transport) {
    echo "  - $transport\n";
}

echo "\nOpenSSL Functions Available:\n";
if (function_exists('openssl_version_string')) {
    echo "  - openssl_version_string(): " . openssl_version_string() . "\n";
}
if (function_exists('openssl_get_cipher_methods')) {
    echo "  - Cipher methods: " . count(openssl_get_cipher_methods()) . " available\n";
}

echo "\n=== Test SSL Connection ===\n";
$transports = stream_get_transports();
if (in_array('ssl', $transports)) {
    echo "SSL transport: AVAILABLE ✓\n";
    echo "TLS transport: " . (in_array('tls', $transports) ? 'AVAILABLE ✓' : 'NOT AVAILABLE ✗') . "\n";
} else {
    echo "SSL transport: NOT AVAILABLE ✗\n";
    echo "\nTo fix:\n";
    echo "1. Enable extension=openssl in php.ini\n";
    echo "2. Restart your PHP server\n";
    echo "3. Check php.ini location: " . php_ini_loaded_file() . "\n";
}





