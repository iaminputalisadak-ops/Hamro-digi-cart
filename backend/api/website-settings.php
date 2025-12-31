<?php
/**
 * Website Settings API
 */
require_once __DIR__ . '/../config/config.php';

// Suppress error display for clean JSON output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

ob_start();

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'GET':
        ob_clean();
        
        // Get all website settings
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings 
                            WHERE setting_key LIKE 'website_%' 
                            OR setting_key LIKE 'logo_%' 
                            OR setting_key LIKE '%_url' 
                            OR setting_key LIKE 'contact_%' 
                            OR setting_key LIKE 'footer_%'
                            OR setting_key LIKE 'product_card_%'
                            OR setting_key LIKE 'banner%'
                            OR setting_key LIKE 'popup%'");
        $settings = $stmt->fetchAll();
        
        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting['setting_key']] = $setting['setting_value'];
        }
        
        // Set defaults if not found
        $defaults = [
            'website_logo' => '',
            'logo_text_line1' => 'Hamro Digi',
            'logo_text_line2' => 'CART',
            'website_title' => 'Hamro Digi Cart',
            'website_tagline' => 'Best Digital Product In India',
            'website_description' => '',
            'facebook_url' => '',
            'instagram_url' => '',
            'youtube_url' => '',
            'twitter_url' => '',
            'whatsapp_url' => '',
            'footer_copyright' => 'Copyright (c) ' . date('Y'),
            'contact_email' => '',
            'contact_phone' => '',
            'contact_address' => '',
            'product_card_button_text' => 'Download',
            'product_card_button_color' => '#6366f1',
            'product_card_default_description' => '',
            'product_card_show_discount_badge' => '1',
            'product_card_discount_badge_text' => '% OFF',
            'product_card_no_products_message' => 'No products available in this category yet.',
            'product_card_see_all_text' => 'See All Products',
            'product_card_promotional_text' => '',
            'product_card_show_price' => '1',
            'product_card_price_label' => '',
            'banner1_title' => 'WE ARE Creators DIGITAL PRODUCT',
            'banner1_subtitle' => 'Sell Digital Products For Free create Store',
            'banner1_image' => '',
            'banner2_title' => 'WE ARE Creators DIGITAL PRODUCT',
            'banner2_subtitle' => 'Digital Products Selling Website',
            'banner2_image' => '',
            'popup_enabled' => '0',
            'popup_title' => '',
            'popup_content' => '',
            'popup_image' => ''
        ];
        
        foreach ($defaults as $key => $defaultValue) {
            if (!isset($settingsArray[$key])) {
                $settingsArray[$key] = $defaultValue;
            }
        }
        
        sendSuccess($settingsArray);
        break;
        
    default:
        ob_end_clean();
        sendError('Method not allowed', 405);
}

