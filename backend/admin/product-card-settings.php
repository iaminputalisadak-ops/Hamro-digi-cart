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
            // Product Details Page Settings
            'product_details_button_text' => $_POST['product_details_button_text'] ?? 'Download',
            'product_details_description_title' => $_POST['product_details_description_title'] ?? '{title} - Fun, Viral & Engaging!',
            'product_details_related_title' => $_POST['product_details_related_title'] ?? 'Get Epic Viral Instagram Reels Bundle For Better Video Content',
            'product_details_ad_title' => $_POST['product_details_ad_title'] ?? 'Amazon Month of the SALE',
            'product_details_ad_description' => $_POST['product_details_ad_description'] ?? 'Special offers and discounts',
            'product_details_features' => $_POST['product_details_features'] ?? "✓ No Logo\n✓ Lifetime Access\n📁 Google Drive Link\n📥 Easy To Download\n✓ No Watermark\n⚡ Instant Download",
            'product_details_default_tags' => $_POST['product_details_default_tags'] ?? 'Skilcart,Reels Kit,Instagram Reels Bundle,Viral Reels Bundle,Animation reels bundle,AI reels bundle,Free download',
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
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'product_card_%' OR setting_key LIKE 'product_details_%'");
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
    <?php include 'includes/favicon.php'; ?>
    <link rel="stylesheet" href="assets/admin.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
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
                                <textarea id="product_card_default_description" name="product_card_default_description" rows="6" placeholder="Enter default description template (leave empty to use product description)"><?php echo htmlspecialchars($settings['product_card_default_description'] ?? ''); ?></textarea>
                                <small style="color: #666;">Default description shown if product doesn't have a description. Use {title} to insert product title. Use the rich text editor to format your description with bold, italic, lists, links, and more.</small>
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
                    
                    <!-- Product Details Page Settings -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">📄 Product Details Page Settings</h2>
                        <div style="padding: 20px;">
                            <div class="form-group">
                                <label>Download Button Text</label>
                                <input type="text" name="product_details_button_text" value="<?php echo htmlspecialchars($settings['product_details_button_text'] ?? 'Download'); ?>" placeholder="Download">
                                <small style="color: #666;">Text on the download button on product details page</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Description Section Title Format</label>
                                <input type="text" name="product_details_description_title" value="<?php echo htmlspecialchars($settings['product_details_description_title'] ?? '{title} - Fun, Viral & Engaging!'); ?>" placeholder="{title} - Fun, Viral & Engaging!">
                                <small style="color: #666;">Use {title} to insert product title. Example: "{title} - Fun, Viral & Engaging!"</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Related Bundles Section Title</label>
                                <input type="text" name="product_details_related_title" value="<?php echo htmlspecialchars($settings['product_details_related_title'] ?? 'Get Epic Viral Instagram Reels Bundle For Better Video Content'); ?>" placeholder="Get Epic Viral Instagram Reels Bundle For Better Video Content">
                                <small style="color: #666;">Title for the related products section in sidebar</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Advertisement Box Title</label>
                                <input type="text" name="product_details_ad_title" value="<?php echo htmlspecialchars($settings['product_details_ad_title'] ?? 'Amazon Month of the SALE'); ?>" placeholder="Amazon Month of the SALE">
                                <small style="color: #666;">Title for the advertisement box in sidebar</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Advertisement Box Description</label>
                                <input type="text" name="product_details_ad_description" value="<?php echo htmlspecialchars($settings['product_details_ad_description'] ?? 'Special offers and discounts'); ?>" placeholder="Special offers and discounts">
                                <small style="color: #666;">Description text for the advertisement box</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Product Features (One per line)</label>
                                <textarea name="product_details_features" rows="8" placeholder="✓ No Logo&#10;✓ Lifetime Access&#10;📁 Google Drive Link&#10;📥 Easy To Download&#10;✓ No Watermark&#10;⚡ Instant Download"><?php echo htmlspecialchars($settings['product_details_features'] ?? "✓ No Logo\n✓ Lifetime Access\n📁 Google Drive Link\n📥 Easy To Download\n✓ No Watermark\n⚡ Instant Download"); ?></textarea>
                                <small style="color: #666;">Enter features, one per line. First character/emoji will be used as icon, rest as text. Example: "✓ No Logo" or "📁 Google Drive Link"</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Default Tags (Comma separated)</label>
                                <input type="text" name="product_details_default_tags" value="<?php echo htmlspecialchars($settings['product_details_default_tags'] ?? 'Skilcart,Reels Kit,Instagram Reels Bundle,Viral Reels Bundle,Animation reels bundle,AI reels bundle,Free download'); ?>" placeholder="Skilcart,Reels Kit,Instagram Reels Bundle">
                                <small style="color: #666;">Default tags to show on product details page (comma separated). Product category will be added automatically.</small>
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
    <script>
        let descriptionEditor;
        
        // Initialize CKEditor for Default Description Template
        ClassicEditor
            .create(document.querySelector('#product_card_default_description'), {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'link', '|',
                        'bulletedList', 'numberedList', '|',
                        'blockQuote', 'insertTable', '|',
                        'undo', 'redo'
                    ]
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .then(editor => {
                descriptionEditor = editor;
                console.log('CKEditor initialized successfully for Default Description Template');
            })
            .catch(error => {
                console.error('Error initializing CKEditor:', error);
            });
        
        // Handle form submission - get data from CKEditor
        document.getElementById('productCardSettingsForm').addEventListener('submit', function(e) {
            // Get content from CKEditor if available
            if (descriptionEditor) {
                const editorData = descriptionEditor.getData();
                // Update the textarea value with CKEditor content
                document.getElementById('product_card_default_description').value = editorData;
            }
            // Form will submit normally
        });
    </script>
</body>
</html>





