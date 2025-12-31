<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$pdo = getDBConnection();
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $settings = [
            'product_card_button_text' => $_POST['product_card_button_text'] ?? 'Download',
            'product_card_button_color' => $_POST['product_card_button_color'] ?? '#6366f1',
            'product_card_default_description' => $_POST['product_card_default_description'] ?? '',
            'product_card_show_discount_badge' => isset($_POST['product_card_show_discount_badge']) ? '1' : '0',
            'product_card_discount_badge_text' => $_POST['product_card_discount_badge_text'] ?? '% OFF',
            'product_card_no_products_message' => $_POST['product_card_no_products_message'] ?? 'No products available in this category yet.',
            'product_card_see_all_text' => $_POST['product_card_see_all_text'] ?? 'See All Products',
            'product_card_promotional_text' => $_POST['product_card_promotional_text'] ?? '',
            'product_card_show_price' => isset($_POST['product_card_show_price']) ? '1' : '0',
            'product_card_price_label' => $_POST['product_card_price_label'] ?? '',
        ];
        
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) 
                                   VALUES (?, ?) 
                                   ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = CURRENT_TIMESTAMP");
            $stmt->execute([$key, $value, $value]);
        }
        
        $message = 'Product card settings saved successfully!';
    } catch (Exception $e) {
        $error = 'Error saving settings: ' . $e->getMessage();
    }
}

// Load existing settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'product_card_%'");
$settingsData = $stmt->fetchAll();
$settings = [];
foreach ($settingsData as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Card Settings - Admin Panel</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <h1>🃏 Product Card Settings</h1>
                <p style="color: #666; margin-bottom: 20px;">Customize how product cards are displayed on the website.</p>
                
                <?php if ($message): ?>
                    <div style="background: #d4edda; color: #155724; padding: 15px; margin: 20px 0; border-radius: 5px;">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px 0; border-radius: 5px;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="productCardSettingsForm" style="max-width: 800px;">
                    <!-- Button Settings -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">🔘 Button Settings</h2>
                        <div style="padding: 20px;">
                            <div class="form-group">
                                <label>Button Text</label>
                                <input type="text" name="product_card_button_text" value="<?php echo htmlspecialchars($settings['product_card_button_text'] ?? 'Download'); ?>" placeholder="Download">
                                <small style="color: #666;">Text displayed on the product card button</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Button Color</label>
                                <input type="color" name="product_card_button_color" value="<?php echo htmlspecialchars($settings['product_card_button_color'] ?? '#6366f1'); ?>">
                                <small style="color: #666;">Color of the product card button</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Price Settings -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">💰 Price Settings</h2>
                        <div style="padding: 20px;">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="product_card_show_price" value="1" <?php echo (isset($settings['product_card_show_price']) && $settings['product_card_show_price'] === '1') || !isset($settings['product_card_show_price']) ? 'checked' : ''; ?>>
                                    Show Price on Product Cards
                                </label>
                            </div>
                            
                            <div class="form-group">
                                <label>Price Label (Optional)</label>
                                <input type="text" name="product_card_price_label" value="<?php echo htmlspecialchars($settings['product_card_price_label'] ?? ''); ?>" placeholder="Starting from">
                                <small style="color: #666;">Optional prefix text before price (e.g., "Starting from", "Only")</small>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="product_card_show_discount_badge" value="1" <?php echo (isset($settings['product_card_show_discount_badge']) && $settings['product_card_show_discount_badge'] === '1') || !isset($settings['product_card_show_discount_badge']) ? 'checked' : ''; ?>>
                                    Show Discount Badge
                                </label>
                            </div>
                            
                            <div class="form-group">
                                <label>Discount Badge Text</label>
                                <input type="text" name="product_card_discount_badge_text" value="<?php echo htmlspecialchars($settings['product_card_discount_badge_text'] ?? '% OFF'); ?>" placeholder="% OFF">
                                <small style="color: #666;">Text shown after discount percentage (e.g., "% OFF", "Discount")</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content Settings -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">📝 Content Settings</h2>
                        <div style="padding: 20px;">
                            <div class="form-group">
                                <label>Default Description Template</label>
                                <textarea name="product_card_default_description" rows="4" placeholder="Enter default description template (leave empty to use product description)"><?php echo htmlspecialchars($settings['product_card_default_description'] ?? ''); ?></textarea>
                                <small style="color: #666;">Default description shown if product doesn't have a description. Use {title} to insert product title.</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Promotional Text (Optional)</label>
                                <input type="text" name="product_card_promotional_text" value="<?php echo htmlspecialchars($settings['product_card_promotional_text'] ?? ''); ?>" placeholder="e.g., Best Seller, New, Limited Time">
                                <small style="color: #666;">Optional promotional text/badge to show on all product cards</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Messages Settings -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">💬 Messages Settings</h2>
                        <div style="padding: 20px;">
                            <div class="form-group">
                                <label>"No Products" Message</label>
                                <input type="text" name="product_card_no_products_message" value="<?php echo htmlspecialchars($settings['product_card_no_products_message'] ?? 'No products available in this category yet.'); ?>" placeholder="No products available in this category yet.">
                                <small style="color: #666;">Message shown when no products are available</small>
                            </div>
                            
                            <div class="form-group">
                                <label>"See All Products" Button Text</label>
                                <input type="text" name="product_card_see_all_text" value="<?php echo htmlspecialchars($settings['product_card_see_all_text'] ?? 'See All Products'); ?>" placeholder="See All Products">
                                <small style="color: #666;">Text on the "See All Products" button</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="action-buttons" style="padding: 20px;">
                        <button type="submit" class="btn btn-primary">💾 Save Product Card Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="assets/admin.js"></script>
</body>
</html>





