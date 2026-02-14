<?php
/**
 * Sitemap generation helpers
 */

require_once __DIR__ . '/../config/database.php';

function getSitemapBaseUrl() {
    // Prefer env (useful for cron)
    $env = getenv('HAMRODIGICART_SITE_URL');
    if ($env && is_string($env) && trim($env) !== '') {
        return rtrim(trim($env), '/');
    }

    // If called from a web request, infer from host
    if (!empty($_SERVER['HTTP_HOST'])) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'];
    }

    // Fallback (edit for production)
    return 'https://hamrodigicart.com';
}

function generateSitemapXml(PDO $pdo, $baseUrl) {
    $baseUrl = rtrim($baseUrl, '/');

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

    // Static pages (front-end routes)
    $staticPages = [
        ['/about-us', 'monthly', 0.8],
        ['/contact-us', 'monthly', 0.8],
        ['/privacy-policy', 'yearly', 0.5],
        ['/terms-conditions', 'yearly', 0.5],
        ['/refund-policy', 'yearly', 0.5],
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

    // Products
    $stmt = $pdo->query("SELECT id, updated_at FROM products WHERE status = 'active' ORDER BY id ASC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($products) {
        $xml .= "  <!-- Products -->\n";
        foreach ($products as $product) {
            $lastmod = !empty($product['updated_at']) ? date('Y-m-d', strtotime($product['updated_at'])) : date('Y-m-d');
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/product/{$product['id']}</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n\n";
        }
    }

    // Categories
    $stmt = $pdo->query("SELECT slug FROM categories WHERE slug IS NOT NULL AND slug <> '' ORDER BY id ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($categories) {
        $xml .= "  <!-- Categories -->\n";
        foreach ($categories as $category) {
            $slug = $category['slug'];
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/{$slug}</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";
            $xml .= "  </url>\n\n";
        }
    }

    $xml .= "</urlset>";
    return $xml;
}

function writePublicSitemap(PDO $pdo, $baseUrl = null) {
    $baseUrl = $baseUrl ?: getSitemapBaseUrl();
    $xml = generateSitemapXml($pdo, $baseUrl);
    $sitemapFile = __DIR__ . '/../../public/sitemap.xml';
    @file_put_contents($sitemapFile, $xml);
    return $xml;
}



