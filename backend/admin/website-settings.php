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
            'website_logo' => $_POST['website_logo'] ?? '',
            'logo_text_line1' => $_POST['logo_text_line1'] ?? 'Hamro Digi',
            'logo_text_line2' => $_POST['logo_text_line2'] ?? 'CART',
            'website_title' => $_POST['website_title'] ?? 'Hamro Digi Cart',
            'website_tagline' => $_POST['website_tagline'] ?? 'Best Digital Product In India',
            'website_description' => $_POST['website_description'] ?? '',
            'facebook_url' => $_POST['facebook_url'] ?? '',
            'instagram_url' => $_POST['instagram_url'] ?? '',
            'youtube_url' => $_POST['youtube_url'] ?? '',
            'twitter_url' => $_POST['twitter_url'] ?? '',
            'whatsapp_url' => $_POST['whatsapp_url'] ?? '',
            'footer_copyright' => $_POST['footer_copyright'] ?? 'Copyright (c) ' . date('Y'),
            'contact_email' => $_POST['contact_email'] ?? '',
            'contact_phone' => $_POST['contact_phone'] ?? '',
            'contact_address' => $_POST['contact_address'] ?? '',
            'banner1_title' => $_POST['banner1_title'] ?? 'WE ARE Creators DIGITAL PRODUCT',
            'banner1_subtitle' => $_POST['banner1_subtitle'] ?? 'Sell Digital Products For Free create Store',
            'banner1_image' => $_POST['banner1_image'] ?? '',
            'banner2_title' => $_POST['banner2_title'] ?? 'WE ARE Creators DIGITAL PRODUCT',
            'banner2_subtitle' => $_POST['banner2_subtitle'] ?? 'Digital Products Selling Website',
            'banner2_image' => $_POST['banner2_image'] ?? '',
            'popup_enabled' => isset($_POST['popup_enabled']) ? '1' : '0',
            'popup_title' => $_POST['popup_title'] ?? '',
            'popup_content' => $_POST['popup_content'] ?? '',
            'popup_image' => $_POST['popup_image'] ?? ''
        ];
        
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) 
                                   VALUES (?, ?) 
                                   ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = CURRENT_TIMESTAMP");
            $stmt->execute([$key, $value, $value]);
        }
        
        $message = 'Website settings saved successfully!';
    } catch (Exception $e) {
        $error = 'Error saving settings: ' . $e->getMessage();
    }
}

// Load existing settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'website_%' OR setting_key LIKE 'logo_%' OR setting_key LIKE '%_url' OR setting_key LIKE 'contact_%' OR setting_key LIKE 'footer_%' OR setting_key LIKE 'banner%' OR setting_key LIKE 'popup%'");
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
    <title>Website Settings - Admin Panel</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <h1>🌐 Website Settings</h1>
                
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
                
                <form method="POST" id="websiteSettingsForm" style="max-width: 800px;">
                    <!-- Logo Settings -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">🖼️ Logo Settings</h2>
                        <div style="padding: 20px;">
                            <div class="form-group">
                                <label>Logo Image</label>
                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                    <div style="flex: 1;">
                                        <input type="file" id="logoFile" accept="image/*" style="margin-bottom: 10px;">
                                        <input type="url" id="website_logo" name="website_logo" placeholder="Or enter logo image URL" value="<?php echo htmlspecialchars($settings['website_logo'] ?? ''); ?>">
                                    </div>
                                    <div id="logoPreview" style="width: 150px; height: 150px; border: 2px dashed #ddd; border-radius: 5px; display: none; align-items: center; justify-content: center; overflow: hidden; background: #f9f9f9;">
                                        <img id="logoPreviewImg" src="" alt="Logo Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    </div>
                                </div>
                                <small style="color: #666;">Upload a logo image or enter an image URL</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Logo Text Line 1</label>
                                <input type="text" name="logo_text_line1" value="<?php echo htmlspecialchars($settings['logo_text_line1'] ?? 'Hamro Digi'); ?>" placeholder="Hamro Digi">
                            </div>
                            
                            <div class="form-group">
                                <label>Logo Text Line 2</label>
                                <input type="text" name="logo_text_line2" value="<?php echo htmlspecialchars($settings['logo_text_line2'] ?? 'CART'); ?>" placeholder="CART">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Website Information -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">📝 Website Information</h2>
                        <div style="padding: 20px;">
                            <div class="form-group">
                                <label>Website Title</label>
                                <input type="text" name="website_title" value="<?php echo htmlspecialchars($settings['website_title'] ?? 'Hamro Digi Cart'); ?>" placeholder="Hamro Digi Cart">
                            </div>
                            
                            <div class="form-group">
                                <label>Website Tagline</label>
                                <input type="text" name="website_tagline" value="<?php echo htmlspecialchars($settings['website_tagline'] ?? 'Best Digital Product In India'); ?>" placeholder="Best Digital Product In India">
                            </div>
                            
                            <div class="form-group">
                                <label>Website Description</label>
                                <textarea name="website_description" rows="4" placeholder="Enter website description"><?php echo htmlspecialchars($settings['website_description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Media Links -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">🔗 Social Media Links</h2>
                        <div style="padding: 20px;">
                            <div class="form-group">
                                <label>Facebook URL</label>
                                <input type="url" name="facebook_url" value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>" placeholder="https://facebook.com/yourpage">
                            </div>
                            
                            <div class="form-group">
                                <label>Instagram URL</label>
                                <input type="url" name="instagram_url" value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>" placeholder="https://instagram.com/yourpage">
                            </div>
                            
                            <div class="form-group">
                                <label>YouTube URL</label>
                                <input type="url" name="youtube_url" value="<?php echo htmlspecialchars($settings['youtube_url'] ?? ''); ?>" placeholder="https://youtube.com/yourchannel">
                            </div>
                            
                            <div class="form-group">
                                <label>Twitter/X URL</label>
                                <input type="url" name="twitter_url" value="<?php echo htmlspecialchars($settings['twitter_url'] ?? ''); ?>" placeholder="https://twitter.com/yourhandle">
                            </div>
                            
                            <div class="form-group">
                                <label>WhatsApp URL</label>
                                <input type="url" name="whatsapp_url" value="<?php echo htmlspecialchars($settings['whatsapp_url'] ?? ''); ?>" placeholder="https://wa.me/1234567890">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">📞 Contact Information</h2>
                        <div style="padding: 20px;">
                            <div class="form-group">
                                <label>Contact Email</label>
                                <input type="email" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>" placeholder="contact@example.com">
                            </div>
                            
                            <div class="form-group">
                                <label>Contact Phone</label>
                                <input type="tel" name="contact_phone" value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>" placeholder="+1234567890">
                            </div>
                            
                            <div class="form-group">
                                <label>Contact Address</label>
                                <textarea name="contact_address" rows="3" placeholder="Enter your business address"><?php echo htmlspecialchars($settings['contact_address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer Settings -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">📄 Footer Settings</h2>
                        <div style="padding: 20px;">
                            <div class="form-group">
                                <label>Footer Copyright Text</label>
                                <input type="text" name="footer_copyright" value="<?php echo htmlspecialchars($settings['footer_copyright'] ?? 'Copyright (c) ' . date('Y')); ?>" placeholder="Copyright (c) 2024">
                                <small style="color: #666;">Use {year} to automatically insert current year</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Banner Settings -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">🎨 Homepage Banner Settings</h2>
                        <div style="padding: 20px;">
                            <!-- Banner 1 -->
                            <h3 style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #ddd;">Banner 1</h3>
                            <div class="form-group">
                                <label>Banner 1 Title</label>
                                <input type="text" name="banner1_title" value="<?php echo htmlspecialchars($settings['banner1_title'] ?? 'WE ARE Creators DIGITAL PRODUCT'); ?>" placeholder="WE ARE Creators DIGITAL PRODUCT">
                            </div>
                            <div class="form-group">
                                <label>Banner 1 Subtitle</label>
                                <input type="text" name="banner1_subtitle" value="<?php echo htmlspecialchars($settings['banner1_subtitle'] ?? 'Sell Digital Products For Free create Store'); ?>" placeholder="Sell Digital Products For Free create Store">
                            </div>
                            <div class="form-group">
                                <label>Banner 1 Image</label>
                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                    <div style="flex: 1;">
                                        <input type="file" id="banner1File" accept="image/*" style="margin-bottom: 10px;">
                                        <input type="url" id="banner1_image" name="banner1_image" placeholder="Or enter banner image URL" value="<?php echo htmlspecialchars($settings['banner1_image'] ?? ''); ?>">
                                    </div>
                                    <div id="banner1Preview" style="width: 150px; height: 150px; border: 2px dashed #ddd; border-radius: 5px; display: none; align-items: center; justify-content: center; overflow: hidden; background: #f9f9f9;">
                                        <img id="banner1PreviewImg" src="" alt="Banner 1 Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    </div>
                                </div>
                                <small style="color: #666;">Upload a banner image or enter an image URL</small>
                            </div>
                            
                            <!-- Banner 2 -->
                            <h3 style="margin-top: 30px; margin-bottom: 15px; padding-top: 20px; padding-bottom: 10px; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">Banner 2</h3>
                            <div class="form-group">
                                <label>Banner 2 Title</label>
                                <input type="text" name="banner2_title" value="<?php echo htmlspecialchars($settings['banner2_title'] ?? 'WE ARE Creators DIGITAL PRODUCT'); ?>" placeholder="WE ARE Creators DIGITAL PRODUCT">
                            </div>
                            <div class="form-group">
                                <label>Banner 2 Subtitle</label>
                                <input type="text" name="banner2_subtitle" value="<?php echo htmlspecialchars($settings['banner2_subtitle'] ?? 'Digital Products Selling Website'); ?>" placeholder="Digital Products Selling Website">
                            </div>
                            <div class="form-group">
                                <label>Banner 2 Image</label>
                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                    <div style="flex: 1;">
                                        <input type="file" id="banner2File" accept="image/*" style="margin-bottom: 10px;">
                                        <input type="url" id="banner2_image" name="banner2_image" placeholder="Or enter banner image URL" value="<?php echo htmlspecialchars($settings['banner2_image'] ?? ''); ?>">
                                    </div>
                                    <div id="banner2Preview" style="width: 150px; height: 150px; border: 2px dashed #ddd; border-radius: 5px; display: none; align-items: center; justify-content: center; overflow: hidden; background: #f9f9f9;">
                                        <img id="banner2PreviewImg" src="" alt="Banner 2 Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    </div>
                                </div>
                                <small style="color: #666;">Upload a banner image or enter an image URL</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Popup Notification Settings -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">🔔 Homepage Popup Notification Settings</h2>
                        <div style="padding: 20px;">
                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 10px;">
                                    <input type="checkbox" name="popup_enabled" value="1" <?php echo (isset($settings['popup_enabled']) && $settings['popup_enabled'] === '1') ? 'checked' : ''; ?>>
                                    <span>Enable popup notification on homepage</span>
                                </label>
                                <small style="color: #666;">When enabled, popup will show every time users visit or refresh the homepage</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Popup Title/Header</label>
                                <input type="text" name="popup_title" value="<?php echo htmlspecialchars($settings['popup_title'] ?? ''); ?>" placeholder="e.g., Today's Schedule, Important Notice">
                            </div>
                            
                            <div class="form-group">
                                <label>Popup Content/Message</label>
                                <textarea name="popup_content" rows="6" placeholder="Enter popup content/message (supports HTML)"><?php echo htmlspecialchars($settings['popup_content'] ?? ''); ?></textarea>
                                <small style="color: #666;">You can use HTML tags for formatting. Use line breaks for multiple lines.</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Popup Image (Optional)</label>
                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                    <div style="flex: 1;">
                                        <input type="file" id="popupFile" accept="image/*" style="margin-bottom: 10px;">
                                        <input type="url" id="popup_image" name="popup_image" placeholder="Or enter popup image URL" value="<?php echo htmlspecialchars($settings['popup_image'] ?? ''); ?>">
                                    </div>
                                    <div id="popupPreview" style="width: 150px; height: 150px; border: 2px dashed #ddd; border-radius: 5px; display: none; align-items: center; justify-content: center; overflow: hidden; background: #f9f9f9;">
                                        <img id="popupPreviewImg" src="" alt="Popup Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    </div>
                                </div>
                                <small style="color: #666;">Upload an image for the popup or enter an image URL (optional)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="action-buttons" style="padding: 20px;">
                        <button type="submit" class="btn btn-primary">💾 Save All Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="assets/admin.js"></script>
    <script>
        const logoFile = document.getElementById('logoFile');
        const logoURL = document.getElementById('website_logo');
        const logoPreview = document.getElementById('logoPreview');
        const logoPreviewImg = document.getElementById('logoPreviewImg');
        
        // Load existing logo preview
        if (logoURL.value) {
            logoPreviewImg.src = logoURL.value;
            logoPreview.style.display = 'flex';
        }
        
        logoFile.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file');
                    this.value = '';
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size should be less than 5MB');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    logoPreviewImg.src = e.target.result;
                    logoPreview.style.display = 'flex';
                    logoURL.value = '';
                };
                reader.onerror = () => {
                    alert('Error reading file');
                    this.value = '';
                };
                reader.readAsDataURL(file);
            }
        });
        
        logoURL.addEventListener('input', function() {
            if (this.value) {
                logoPreviewImg.src = this.value;
                logoPreview.style.display = 'flex';
                logoFile.value = '';
            } else {
                logoPreview.style.display = 'none';
                logoPreviewImg.src = '';
            }
        });
        
        // Banner 1 image handling
        const banner1File = document.getElementById('banner1File');
        const banner1URL = document.getElementById('banner1_image');
        const banner1Preview = document.getElementById('banner1Preview');
        const banner1PreviewImg = document.getElementById('banner1PreviewImg');
        
        // Load existing banner 1 preview
        if (banner1URL.value) {
            banner1PreviewImg.src = banner1URL.value;
            banner1Preview.style.display = 'flex';
        }
        
        banner1File.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file');
                    this.value = '';
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size should be less than 5MB');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    banner1PreviewImg.src = e.target.result;
                    banner1Preview.style.display = 'flex';
                    banner1URL.value = '';
                };
                reader.onerror = () => {
                    alert('Error reading file');
                    this.value = '';
                };
                reader.readAsDataURL(file);
            }
        });
        
        banner1URL.addEventListener('input', function() {
            if (this.value) {
                banner1PreviewImg.src = this.value;
                banner1Preview.style.display = 'flex';
                banner1File.value = '';
            } else {
                banner1Preview.style.display = 'none';
                banner1PreviewImg.src = '';
            }
        });
        
        // Banner 2 image handling
        const banner2File = document.getElementById('banner2File');
        const banner2URL = document.getElementById('banner2_image');
        const banner2Preview = document.getElementById('banner2Preview');
        const banner2PreviewImg = document.getElementById('banner2PreviewImg');
        
        // Load existing banner 2 preview
        if (banner2URL.value) {
            banner2PreviewImg.src = banner2URL.value;
            banner2Preview.style.display = 'flex';
        }
        
        banner2File.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file');
                    this.value = '';
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size should be less than 5MB');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    banner2PreviewImg.src = e.target.result;
                    banner2Preview.style.display = 'flex';
                    banner2URL.value = '';
                };
                reader.onerror = () => {
                    alert('Error reading file');
                    this.value = '';
                };
                reader.readAsDataURL(file);
            }
        });
        
        banner2URL.addEventListener('input', function() {
            if (this.value) {
                banner2PreviewImg.src = this.value;
                banner2Preview.style.display = 'flex';
                banner2File.value = '';
            } else {
                banner2Preview.style.display = 'none';
                banner2PreviewImg.src = '';
            }
        });
        
        // Popup image handling
        const popupFile = document.getElementById('popupFile');
        const popupURL = document.getElementById('popup_image');
        const popupPreview = document.getElementById('popupPreview');
        const popupPreviewImg = document.getElementById('popupPreviewImg');
        
        // Load existing popup preview
        if (popupURL.value) {
            popupPreviewImg.src = popupURL.value;
            popupPreview.style.display = 'flex';
        }
        
        popupFile.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file');
                    this.value = '';
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size should be less than 5MB');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    popupPreviewImg.src = e.target.result;
                    popupPreview.style.display = 'flex';
                    popupURL.value = '';
                };
                reader.onerror = () => {
                    alert('Error reading file');
                    this.value = '';
                };
                reader.readAsDataURL(file);
            }
        });
        
        popupURL.addEventListener('input', function() {
            if (this.value) {
                popupPreviewImg.src = this.value;
                popupPreview.style.display = 'flex';
                popupFile.value = '';
            } else {
                popupPreview.style.display = 'none';
                popupPreviewImg.src = '';
            }
        });
        
        document.getElementById('websiteSettingsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
            
            try {
                let logoUrl = logoURL.value.trim();
                
                // Upload logo file if selected
                if (logoFile.files && logoFile.files.length > 0) {
                    const file = logoFile.files[0];
                    const formData = new FormData();
                    formData.append('file', file);
                    
                    try {
                        const uploadResponse = await fetch('../api/upload.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        if (!uploadResponse.ok) {
                            throw new Error('Upload request failed');
                        }
                        
                        const uploadData = await uploadResponse.json();
                        
                        if (uploadData.success) {
                            if (uploadData.data && uploadData.data.url) {
                                logoUrl = uploadData.data.url;
                            } else if (uploadData.url) {
                                logoUrl = uploadData.url;
                            }
                            logoURL.value = logoUrl;
                        } else {
                            throw new Error(uploadData.error || 'Upload failed');
                        }
                    } catch (error) {
                        alert('Logo upload failed: ' + error.message);
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                        return;
                    }
                }
                
                // Upload banner 1 file if selected
                let banner1Url = banner1URL.value.trim();
                if (banner1File.files && banner1File.files.length > 0) {
                    const file = banner1File.files[0];
                    const formData = new FormData();
                    formData.append('file', file);
                    
                    try {
                        const uploadResponse = await fetch('../api/upload.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        if (!uploadResponse.ok) {
                            throw new Error('Upload request failed');
                        }
                        
                        const uploadData = await uploadResponse.json();
                        
                        if (uploadData.success) {
                            if (uploadData.data && uploadData.data.url) {
                                banner1Url = uploadData.data.url;
                            } else if (uploadData.url) {
                                banner1Url = uploadData.url;
                            }
                            banner1URL.value = banner1Url;
                        } else {
                            throw new Error(uploadData.error || 'Upload failed');
                        }
                    } catch (error) {
                        alert('Banner 1 upload failed: ' + error.message);
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                        return;
                    }
                }
                
                // Upload banner 2 file if selected
                let banner2Url = banner2URL.value.trim();
                if (banner2File.files && banner2File.files.length > 0) {
                    const file = banner2File.files[0];
                    const formData = new FormData();
                    formData.append('file', file);
                    
                    try {
                        const uploadResponse = await fetch('../api/upload.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        if (!uploadResponse.ok) {
                            throw new Error('Upload request failed');
                        }
                        
                        const uploadData = await uploadResponse.json();
                        
                        if (uploadData.success) {
                            if (uploadData.data && uploadData.data.url) {
                                banner2Url = uploadData.data.url;
                            } else if (uploadData.url) {
                                banner2Url = uploadData.url;
                            }
                            banner2URL.value = banner2Url;
                        } else {
                            throw new Error(uploadData.error || 'Upload failed');
                        }
                    } catch (error) {
                        alert('Banner 2 upload failed: ' + error.message);
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                        return;
                    }
                }
                
                // Upload popup file if selected
                let popupUrl = popupURL.value.trim();
                if (popupFile.files && popupFile.files.length > 0) {
                    const file = popupFile.files[0];
                    const formData = new FormData();
                    formData.append('file', file);
                    
                    try {
                        const uploadResponse = await fetch('../api/upload.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        if (!uploadResponse.ok) {
                            throw new Error('Upload request failed');
                        }
                        
                        const uploadData = await uploadResponse.json();
                        
                        if (uploadData.success) {
                            if (uploadData.data && uploadData.data.url) {
                                popupUrl = uploadData.data.url;
                            } else if (uploadData.url) {
                                popupUrl = uploadData.url;
                            }
                            popupURL.value = popupUrl;
                        } else {
                            throw new Error(uploadData.error || 'Upload failed');
                        }
                    } catch (error) {
                        alert('Popup image upload failed: ' + error.message);
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                        return;
                    }
                }
                
                // Submit form
                this.submit();
            } catch (error) {
                console.error('Error:', error);
                alert('Error: ' + error.message);
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
    </script>
</body>
</html>



