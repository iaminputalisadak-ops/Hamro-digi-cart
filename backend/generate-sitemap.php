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

require_once __DIR__ . '/config/database.php';

$baseUrl = 'https://hamrodigicart.com';
$sitemapFile = __DIR__ . '/../public/sitemap.xml';

try {
    $pdo = getDBConnection();
    
    // Start XML
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
    $xml .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
    $xml .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n";
    $xml .= '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n\n";

    // Homepage
    $xml .= "  <!-- Homepage -->\n";
    $xml .= "  <url>\n";
    $xml .= "    <loc>{$baseUrl}/</loc>\n";
    $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
    $xml .= "    <changefreq>daily</changefreq>\n";
    $xml .= "    <priority>1.0</priority>\n";
    $xml .= "  </url>\n\n";

    // Static Pages
    $staticPages = [
        ['/about-us', 'monthly', 0.8],
        ['/contact-us', 'monthly', 0.8],
        ['/privacy-policy', 'yearly', 0.5],
        ['/terms-conditions', 'yearly', 0.5],
        ['/refund-policy', 'yearly', 0.5],
        ['/all-products', 'daily', 0.9],
        ['/free-products', 'weekly', 0.8],
        ['/premium-products', 'daily', 0.8],
    ];

    $xml .= "  <!-- Static Pages -->\n";
    foreach ($staticPages as $page) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>{$baseUrl}{$page[0]}</loc>\n";
        $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $xml .= "    <changefreq>{$page[1]}</changefreq>\n";
        $xml .= "    <priority>{$page[2]}</priority>\n";
        $xml .= "  </url>\n\n";
    }

    // Get all active products
    $stmt = $pdo->query("SELECT id, updated_at FROM products WHERE status = 'active' ORDER BY id ASC");
    $products = $stmt->fetchAll();

    if (count($products) > 0) {
        $xml .= "  <!-- Products -->\n";
        foreach ($products as $product) {
            $lastmod = $product['updated_at'] ? date('Y-m-d', strtotime($product['updated_at'])) : date('Y-m-d');
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/product/{$product['id']}</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n\n";
        }
    }

    // Get all categories
    $stmt = $pdo->query("SELECT slug FROM categories WHERE slug IS NOT NULL AND slug != '' ORDER BY id ASC");
    $categories = $stmt->fetchAll();

    if (count($categories) > 0) {
        $xml .= "  <!-- Categories -->\n";
        foreach ($categories as $category) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/{$category['slug']}</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";
            $xml .= "  </url>\n\n";
        }
    }

    // Get all active offers
    $stmt = $pdo->query("SELECT title FROM offers WHERE status = 'active' ORDER BY id ASC");
    $offers = $stmt->fetchAll();

    if (count($offers) > 0) {
        $xml .= "  <!-- Offers -->\n";
        foreach ($offers as $offer) {
            // Generate slug from title
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $offer['title'])));
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');
            
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/{$slug}</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";
            $xml .= "  </url>\n\n";
        }
    }

    // Close XML
    $xml .= "</urlset>";

    // Write to file
    if (file_put_contents($sitemapFile, $xml)) {
        if (php_sapi_name() === 'cli') {
            echo "✓ Sitemap generated successfully: {$sitemapFile}\n";
            echo "Total URLs: " . (1 + count($staticPages) + count($products) + count($categories)) . "\n";
        } else {
            header('Content-Type: text/xml; charset=utf-8');
            echo $xml;
        }
    } else {
        throw new Exception("Failed to write sitemap file");
    }

} catch (Exception $e) {
    if (php_sapi_name() === 'cli') {
        echo "✗ Error generating sitemap: " . $e->getMessage() . "\n";
    } else {
        http_response_code(500);
        echo "Error generating sitemap: " . $e->getMessage();
    }
}




