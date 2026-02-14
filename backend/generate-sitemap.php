<?php
/**
 * Generate Dynamic Sitemap.xml
 * 
 * This script generates a sitemap.xml file including all products dynamically.
 * Run this script periodically (via cron job) to update your sitemap.
 * 
 * Access via: https://hamrodigicart.com/backend/generate-sitemap.php
 * Or run via command line: php generate-sitemap.php
 */

require_once __DIR__ . '/utils/sitemap.php';

try {
    $pdo = getDBConnection();
    $baseUrl = getSitemapBaseUrl();
    $xml = writePublicSitemap($pdo, $baseUrl);

    if (php_sapi_name() === 'cli') {
        echo "✓ Sitemap generated successfully: public/sitemap.xml\n";
        echo "Base URL: {$baseUrl}\n";
    } else {
        header('Content-Type: text/xml; charset=utf-8');
        echo $xml;
    }

} catch (Exception $e) {
    if (php_sapi_name() === 'cli') {
        echo "✗ Error generating sitemap: " . $e->getMessage() . "\n";
    } else {
        http_response_code(500);
        echo "Error generating sitemap: " . $e->getMessage();
    }
}




