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
            'website_favicon' => $_POST['website_favicon'] ?? '',
            'logo_text_line1' => $_POST['logo_text_line1'] ?? 'Hamro Digi',
            'logo_text_line2' => $_POST['logo_text_line2'] ?? 'CART',
            'website_title' => $_POST['website_title'] ?? 'Hamro Digi Cart',
            'website_tagline' => $_POST['website_tagline'] ?? 'Best Digital Product In India',
            'website_description' => $_POST['website_description'] ?? '',
            'facebook_url' => $_POST['facebook_url'] ?? '',
            'facebook_name' => $_POST['facebook_name'] ?? 'Facebook',
            'facebook_icon_url' => $_POST['facebook_icon_url'] ?? '',
            'instagram_url' => $_POST['instagram_url'] ?? '',
            'instagram_name' => $_POST['instagram_name'] ?? 'Instagram',
            'instagram_icon_url' => $_POST['instagram_icon_url'] ?? '',
            'youtube_url' => $_POST['youtube_url'] ?? '',
            'youtube_name' => $_POST['youtube_name'] ?? 'YouTube',
            'youtube_icon_url' => $_POST['youtube_icon_url'] ?? '',
            'twitter_url' => $_POST['twitter_url'] ?? '',
            'twitter_name' => $_POST['twitter_name'] ?? 'Twitter/X',
            'twitter_icon_url' => $_POST['twitter_icon_url'] ?? '',
            'whatsapp_url' => $_POST['whatsapp_url'] ?? '',
            'whatsapp_name' => $_POST['whatsapp_name'] ?? 'WhatsApp',
            'whatsapp_icon_url' => $_POST['whatsapp_icon_url'] ?? '',
            'footer_copyright' => $_POST['footer_copyright'] ?? 'Copyright (c) ' . date('Y'),
            'admin_order_notification_email' => $_POST['admin_order_notification_email'] ?? '',
            'contact_email' => $_POST['contact_email'] ?? '',
            'contact_phone' => $_POST['contact_phone'] ?? '',
            'contact_address' => $_POST['contact_address'] ?? '',
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
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings 
                     WHERE setting_key LIKE 'website_%' 
                        OR setting_key LIKE 'logo_%' 
                        OR setting_key LIKE '%_url' 
                        OR setting_key LIKE '%_name'
                        OR setting_key LIKE 'contact_%' 
                        OR setting_key LIKE 'footer_%' 
                        OR setting_key LIKE 'banner%' 
                        OR setting_key LIKE 'popup%'");
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
    <?php include 'includes/favicon.php'; ?>
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

                    <!-- Favicon Settings -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">🌟 Favicon Settings</h2>
                        <div style="padding: 20px;">
                            <div class="form-group">
                                <label>Website Favicon</label>
                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                    <div style="flex: 1;">
                                        <input type="file" id="faviconFile" accept="image/*" style="margin-bottom: 10px;">
                                        <input type="url" id="website_favicon" name="website_favicon" placeholder="Or enter favicon image URL" value="<?php echo htmlspecialchars($settings['website_favicon'] ?? ''); ?>">
                                    </div>
                                    <div id="faviconPreview" style="width: 64px; height: 64px; border: 2px dashed #ddd; border-radius: 5px; display: none; align-items: center; justify-content: center; overflow: hidden; background: #f9f9f9;">
                                        <img id="faviconPreviewImg" src="" alt="Favicon Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    </div>
                                </div>
                                <small style="color: #666;">Upload a favicon (PNG recommended) or enter an image URL. This will apply to both the homepage and admin panel.</small>
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
                            <p style="margin: 0 0 14px 0; color: #666; font-size: 13px;">
                                You can change the <strong>name</strong>, <strong>icon</strong> and <strong>URL</strong>. Icons support PNG/JPG/WebP/GIF/ICO uploads.
                            </p>

                            <?php
                              $socialPlatforms = [
                                ['key' => 'facebook', 'label' => 'Facebook'],
                                ['key' => 'instagram', 'label' => 'Instagram'],
                                ['key' => 'youtube', 'label' => 'YouTube'],
                                ['key' => 'twitter', 'label' => 'Twitter/X'],
                                ['key' => 'whatsapp', 'label' => 'WhatsApp'],
                              ];
                            ?>

                            <div style="display: grid; grid-template-columns: 1fr; gap: 14px;">
                                <?php foreach ($socialPlatforms as $p): 
                                  $k = $p['key'];
                                  $nameKey = $k . '_name';
                                  $iconKey = $k . '_icon_url';
                                  $urlKey = $k . '_url';
                                ?>
                                  <div style="border: 1px solid #eee; border-radius: 10px; padding: 14px; background: #fff;">
                                    <div style="display:flex; align-items:center; justify-content:space-between; gap: 12px; margin-bottom: 10px;">
                                      <div style="font-weight: 700; color: #111827;"><?php echo htmlspecialchars($p['label']); ?></div>
                                      <div id="<?php echo $k; ?>IconPreview" style="width: 44px; height: 44px; border: 1px dashed #ddd; border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f9fafb;">
                                        <?php if (!empty($settings[$iconKey] ?? '')): ?>
                                          <img id="<?php echo $k; ?>IconPreviewImg" src="<?php echo htmlspecialchars($settings[$iconKey]); ?>" alt="<?php echo htmlspecialchars($p['label']); ?> icon" style="width: 100%; height: 100%; object-fit: contain;">
                                        <?php else: ?>
                                          <img id="<?php echo $k; ?>IconPreviewImg" src="" alt="<?php echo htmlspecialchars($p['label']); ?> icon" style="width: 100%; height: 100%; object-fit: contain; display:none;">
                                          <span style="color:#9ca3af; font-size: 11px;">icon</span>
                                        <?php endif; ?>
                                      </div>
                                    </div>

                                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                      <div class="form-group" style="margin: 0;">
                                        <label for="<?php echo $k; ?>_name">Display Name</label>
                                        <input id="<?php echo $k; ?>_name" type="text" name="<?php echo $nameKey; ?>" value="<?php echo htmlspecialchars($settings[$nameKey] ?? $p['label']); ?>" placeholder="<?php echo htmlspecialchars($p['label']); ?>">
                                      </div>
                                      <div class="form-group" style="margin: 0;">
                                        <label for="<?php echo $k; ?>_url">URL</label>
                                        <input id="<?php echo $k; ?>_url" type="url" name="<?php echo $urlKey; ?>" value="<?php echo htmlspecialchars($settings[$urlKey] ?? ''); ?>" placeholder="https://...">
                                      </div>
                                    </div>

                                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 10px;">
                                      <div class="form-group" style="margin: 0;">
                                        <label for="<?php echo $k; ?>IconFile">Upload Icon (Optional)</label>
                                        <input type="file" id="<?php echo $k; ?>IconFile" accept="image/*">
                                        <small style="color:#666;">Recommended: square icon (e.g. 64×64)</small>
                                      </div>
                                      <div class="form-group" style="margin: 0;">
                                        <label for="<?php echo $k; ?>_icon_url">Icon URL (Optional)</label>
                                        <input id="<?php echo $k; ?>_icon_url" type="url" name="<?php echo $iconKey; ?>" value="<?php echo htmlspecialchars($settings[$iconKey] ?? ''); ?>" placeholder="https://...">
                                      </div>
                                    </div>
                                  </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="data-table" style="margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">📞 Contact Information</h2>
                        <div style="padding: 20px;">
                            <div class="form-group">
                                <label>Admin Order Notification Email</label>
                                <input type="email" name="admin_order_notification_email" value="<?php echo htmlspecialchars($settings['admin_order_notification_email'] ?? ''); ?>" placeholder="admin@yourdomain.com">
                                <small style="color: #666;">New order emails (purchase notifications) will be sent here. Recommended: your CPanel email.</small>
                            </div>

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

        const faviconFile = document.getElementById('faviconFile');
        const faviconURL = document.getElementById('website_favicon');
        const faviconPreview = document.getElementById('faviconPreview');
        const faviconPreviewImg = document.getElementById('faviconPreviewImg');
        
        // Load existing logo preview
        if (logoURL.value) {
            logoPreviewImg.src = logoURL.value;
            logoPreview.style.display = 'flex';
        }

        // Load existing favicon preview
        if (faviconURL.value) {
            faviconPreviewImg.src = faviconURL.value;
            faviconPreview.style.display = 'flex';
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

        faviconFile.addEventListener('change', function() {
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
                    faviconPreviewImg.src = e.target.result;
                    faviconPreview.style.display = 'flex';
                    faviconURL.value = '';
                };
                reader.onerror = () => {
                    alert('Error reading file');
                    this.value = '';
                };
                reader.readAsDataURL(file);
            }
        });

        faviconURL.addEventListener('input', function() {
            if (this.value) {
                faviconPreviewImg.src = this.value;
                faviconPreview.style.display = 'flex';
                faviconFile.value = '';
            } else {
                faviconPreview.style.display = 'none';
                faviconPreviewImg.src = '';
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

        // Social icon previews + optional uploads
        const socialPlatforms = ['facebook', 'instagram', 'youtube', 'twitter', 'whatsapp'];
        socialPlatforms.forEach((k) => {
            const fileEl = document.getElementById(`${k}IconFile`);
            const urlEl = document.getElementById(`${k}_icon_url`);
            const previewWrap = document.getElementById(`${k}IconPreview`);
            const previewImg = document.getElementById(`${k}IconPreviewImg`);
            if (!fileEl || !urlEl || !previewWrap || !previewImg) return;

            const setPreview = (src) => {
                if (src) {
                    previewImg.src = src;
                    previewImg.style.display = 'block';
                    // hide placeholder text if present
                    const placeholder = previewWrap.querySelector('span');
                    if (placeholder) placeholder.style.display = 'none';
                } else {
                    previewImg.src = '';
                    previewImg.style.display = 'none';
                    const placeholder = previewWrap.querySelector('span');
                    if (placeholder) placeholder.style.display = 'block';
                }
            };

            fileEl.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    setPreview(e.target.result);
                    urlEl.value = '';
                };
                reader.onerror = () => {
                    alert('Error reading file');
                    this.value = '';
                };
                reader.readAsDataURL(file);
            });

            urlEl.addEventListener('input', function() {
                const v = this.value.trim();
                if (v) {
                    setPreview(v);
                    fileEl.value = '';
                } else {
                    setPreview('');
                }
            });
        });
        
        document.getElementById('websiteSettingsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
            
            try {
                let logoUrl = logoURL.value.trim();
                let faviconUrl = faviconURL.value.trim();
                
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

                // Upload favicon file if selected
                if (faviconFile.files && faviconFile.files.length > 0) {
                    const file = faviconFile.files[0];
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
                                faviconUrl = uploadData.data.url;
                            } else if (uploadData.url) {
                                faviconUrl = uploadData.url;
                            }
                            faviconURL.value = faviconUrl;
                        } else {
                            throw new Error(uploadData.error || 'Upload failed');
                        }
                    } catch (error) {
                        alert('Favicon upload failed: ' + error.message);
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

                // Upload social icons if selected
                for (const k of socialPlatforms) {
                    const fileEl = document.getElementById(`${k}IconFile`);
                    const urlEl = document.getElementById(`${k}_icon_url`);
                    if (!fileEl || !urlEl) continue;

                    let iconUrl = (urlEl.value || '').trim();
                    if (fileEl.files && fileEl.files.length > 0) {
                        const file = fileEl.files[0];
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
                                    iconUrl = uploadData.data.url;
                                } else if (uploadData.url) {
                                    iconUrl = uploadData.url;
                                }
                                urlEl.value = iconUrl;
                            } else {
                                throw new Error(uploadData.error || 'Upload failed');
                            }
                        } catch (error) {
                            alert(`${k} icon upload failed: ` + error.message);
                            submitButton.disabled = false;
                            submitButton.textContent = originalText;
                            return;
                        }
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



