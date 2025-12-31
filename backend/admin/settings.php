<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$pdo = getDBConnection();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($currentPassword, $admin['password'])) {
            if ($newPassword === $confirmPassword) {
                if (strlen($newPassword) >= 4) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
                    $stmt->execute([$hashedPassword, $_SESSION['admin_id']]);
                    $message = 'Password changed successfully!';
                } else {
                    $error = 'Password must be at least 4 characters long';
                }
            } else {
                $error = 'New passwords do not match';
            }
        } else {
            $error = 'Current password is incorrect';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <h1>Settings</h1>
                
                <!-- Change Password Section -->
                <div class="data-table" style="max-width: 600px; margin-bottom: 30px;">
                    <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">Change Password</h2>
                    
                    <?php if ($message): ?>
                        <div style="background: #d4edda; color: #155724; padding: 15px; margin: 20px; border-radius: 5px;">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 5px;">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" style="padding: 20px;">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required minlength="4">
                        </div>
                        
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" required minlength="4">
                        </div>
                        
                        <div class="action-buttons">
                            <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                        </div>
                    </form>
                </div>
                
                <!-- SMTP Email Settings Section -->
                <div class="data-table" style="max-width: 600px;">
                    <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">📧 SMTP Email Settings</h2>
                    <p style="padding: 0 20px; color: #666; margin-bottom: 20px;">
                        Configure SMTP settings to send product links to customers via email.
                    </p>
                    
                    <div id="smtpMessage" style="display: none; padding: 15px; margin: 20px; border-radius: 5px;"></div>
                    
                    <form id="smtpForm" style="padding: 20px;">
                        <div class="form-group">
                            <label>SMTP Host</label>
                            <input type="text" id="smtpHost" placeholder="smtp.gmail.com" required>
                            <small style="color: #666;">Gmail: smtp.gmail.com | Outlook: smtp-mail.outlook.com</small>
                        </div>
                        
                        <div class="form-group">
                            <label>SMTP Port</label>
                            <input type="number" id="smtpPort" placeholder="587" value="587" required>
                            <small style="color: #666;">Common ports: 587 (TLS) or 465 (SSL)</small>
                        </div>
                        
                        <div class="form-group">
                            <label>SMTP Encryption</label>
                            <select id="smtpEncryption" required>
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="">None</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>SMTP Email (From Email)</label>
                            <input type="email" id="smtpEmail" placeholder="your-email@gmail.com" required>
                            <small style="color: #666;">This email will be used to send product links</small>
                        </div>
                        
                        <div class="form-group">
                            <label>SMTP Password</label>
                            <div style="position: relative;">
                                <input type="password" id="smtpPassword" placeholder="Your email password or app password" required style="padding-right: 40px;">
                                <button type="button" id="togglePassword" onclick="togglePasswordVisibility()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 18px; padding: 5px;">👁️</button>
                            </div>
                            <small style="color: #666;">For Gmail, use App Password (not regular password). Click 👁️ to show/hide password. Spaces in App Password (like "qtsi ihcd gusz xmoo") are preserved.</small>
                        </div>
                        
                        <div class="form-group">
                            <label>From Name (Optional)</label>
                            <input type="text" id="smtpFromName" placeholder="Hamro Digi Cart">
                        </div>
                        
                        <div class="action-buttons">
                            <button type="submit" class="btn btn-primary">💾 Save SMTP Settings</button>
                            <button type="button" class="btn btn-secondary" onclick="testSMTP()">🧪 Test Email</button>
                        </div>
                    </form>
                </div>
                
                <!-- QR Code Settings Section -->
                <div class="data-table" style="max-width: 600px; margin-top: 30px;">
                    <h2 style="margin-bottom: 20px; padding: 20px 20px 0;">📱 Payment QR Code</h2>
                    <p style="padding: 0 20px; color: #666; margin-bottom: 20px;">
                        Upload or update the payment QR code that customers will see on the payment page.
                    </p>
                    
                    <div id="qrMessage" style="display: none; padding: 15px; margin: 20px; border-radius: 5px;"></div>
                    
                    <form id="qrForm" style="padding: 20px;">
                        <div class="form-group">
                            <label>QR Code Image</label>
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <div style="flex: 1;">
                                    <input type="file" id="qrCodeFile" accept="image/*" style="margin-bottom: 10px;">
                                    <input type="url" id="qrCodeURL" placeholder="Or enter QR code image URL">
                                </div>
                                <div id="qrPreview" style="width: 150px; height: 150px; border: 2px dashed #ddd; border-radius: 5px; display: none; align-items: center; justify-content: center; overflow: hidden; background: #f9f9f9;">
                                    <img id="qrPreviewImg" src="" alt="QR Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                </div>
                            </div>
                            <small style="color: #666; display: block; margin-top: 5px;">Upload an image or enter an image URL</small>
                        </div>
                        
                        <div class="action-buttons">
                            <button type="submit" class="btn btn-primary">💾 Save QR Code</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/admin.js"></script>
    <script>
        const settingsApiUrl = '../api/settings.php';
        
        // Load existing SMTP settings
        function loadSMTPSettings() {
            fetch(settingsApiUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (data.data.smtp_host) document.getElementById('smtpHost').value = data.data.smtp_host;
                        if (data.data.smtp_port) document.getElementById('smtpPort').value = data.data.smtp_port;
                        if (data.data.smtp_encryption) document.getElementById('smtpEncryption').value = data.data.smtp_encryption;
                        if (data.data.smtp_email) document.getElementById('smtpEmail').value = data.data.smtp_email;
                        if (data.data.smtp_password) document.getElementById('smtpPassword').value = data.data.smtp_password;
                        if (data.data.smtp_from_name) document.getElementById('smtpFromName').value = data.data.smtp_from_name;
                    }
                })
                .catch(error => console.error('Error loading settings:', error));
        }
        
        // Save SMTP settings
        document.getElementById('smtpForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Get password value - preserve spaces (Gmail App Passwords have spaces like "qtsi ihcd gusz xmoo")
            const passwordValue = document.getElementById('smtpPassword').value;
            
            const settings = {
                smtp_host: document.getElementById('smtpHost').value.trim(),
                smtp_port: document.getElementById('smtpPort').value.trim(),
                smtp_encryption: document.getElementById('smtpEncryption').value.trim(),
                smtp_email: document.getElementById('smtpEmail').value.trim(),
                smtp_password: passwordValue, // Don't trim password - preserve spaces for App Passwords
                smtp_from_name: (document.getElementById('smtpFromName').value || 'Hamro Digi Cart').trim()
            };
            
            // Validate required fields
            if (!settings.smtp_host || !settings.smtp_email || !settings.smtp_password) {
                showMessage('Please fill in all required SMTP fields (Host, Email, and Password)', 'error');
                return;
            }
            
            // Save each setting
            const savePromises = Object.keys(settings).map(key => {
                return fetch(settingsApiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ key: key, value: settings[key] })
                });
            });
            
            try {
                await Promise.all(savePromises);
                showMessage('SMTP settings saved successfully!', 'success');
            } catch (error) {
                showMessage('Error saving settings: ' + error.message, 'error');
            }
        });
        
        // Test SMTP
        function testSMTP() {
            const email = document.getElementById('smtpEmail').value;
            if (!email) {
                alert('Please enter SMTP email first');
                return;
            }
            
            if (confirm('Send test email to ' + email + '?')) {
                fetch('../api/send-email.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        to: email,
                        subject: 'Test Email - SMTP Configuration',
                        message: '<h2>Test Email</h2><p>If you received this email, your SMTP configuration is working correctly!</p>',
                        test: true
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showMessage('Test email sent! Check your inbox.', 'success');
                    } else {
                        showMessage('Error: ' + data.error, 'error');
                    }
                })
                .catch(error => {
                    showMessage('Error: ' + error.message, 'error');
                });
            }
        }
        
        function showMessage(message, type) {
            const msgDiv = document.getElementById('smtpMessage');
            msgDiv.textContent = message;
            msgDiv.style.display = 'block';
            msgDiv.style.background = type === 'success' ? '#d4edda' : '#f8d7da';
            msgDiv.style.color = type === 'success' ? '#155724' : '#721c24';
            
            setTimeout(() => {
                msgDiv.style.display = 'none';
            }, 5000);
        }
        
        // Toggle password visibility
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('smtpPassword');
            const toggleButton = document.getElementById('togglePassword');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = '🙈';
                toggleButton.title = 'Hide Password';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = '👁️';
                toggleButton.title = 'Show Password';
            }
        }
        
        // Toggle password visibility
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('smtpPassword');
            const toggleButton = document.getElementById('togglePassword');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = '🙈';
                toggleButton.title = 'Hide Password';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = '👁️';
                toggleButton.title = 'Show Password';
            }
        }
        
        // Load settings on page load
        loadSMTPSettings();
        loadQRCodeSettings();
        
        // QR Code Management
        const qrCodeFile = document.getElementById('qrCodeFile');
        const qrCodeURL = document.getElementById('qrCodeURL');
        const qrPreview = document.getElementById('qrPreview');
        const qrPreviewImg = document.getElementById('qrPreviewImg');
        
        qrCodeFile.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file');
                    this.value = '';
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size should be less than 5MB');
                    this.value = '';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    qrPreviewImg.src = e.target.result;
                    qrPreview.style.display = 'flex';
                    qrCodeURL.value = ''; // Clear URL if file is selected
                };
                reader.onerror = () => {
                    alert('Error reading file');
                    this.value = '';
                };
                reader.readAsDataURL(file);
            } else {
                // If no file selected and no URL, hide preview
                if (!qrCodeURL.value) {
                    qrPreview.style.display = 'none';
                    qrPreviewImg.src = '';
                }
            }
        });
        
        qrCodeURL.addEventListener('input', function() {
            if (this.value) {
                qrPreviewImg.src = this.value;
                qrPreview.style.display = 'flex';
                qrCodeFile.value = ''; // Clear file if URL is entered
            } else {
                qrPreview.style.display = 'none';
                qrPreviewImg.src = '';
            }
        });
        
        function loadQRCodeSettings() {
            fetch(settingsApiUrl + '?key=payment_qr_code')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data && data.data.value) {
                        const qrUrl = data.data.value;
                        qrCodeURL.value = qrUrl;
                        qrPreviewImg.src = qrUrl;
                        qrPreview.style.display = 'flex';
                    }
                })
                .catch(error => console.error('Error loading QR code:', error));
        }
        
        document.getElementById('qrForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
            
            try {
                let qrCodeUrl = qrCodeURL.value.trim();
                
                // Check if file is selected
                if (qrCodeFile.files && qrCodeFile.files.length > 0) {
                    const file = qrCodeFile.files[0];
                    
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        showQRMessage('Please select an image file', 'error');
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                        return;
                    }
                    
                    // Validate file size (max 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        showQRMessage('Image size should be less than 5MB', 'error');
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                        return;
                    }
                    
                    // Upload file
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
                            // Check both possible response structures
                            if (uploadData.data && uploadData.data.url) {
                                qrCodeUrl = uploadData.data.url;
                            } else if (uploadData.url) {
                                qrCodeUrl = uploadData.url;
                            } else {
                                throw new Error('Invalid upload response format');
                            }
                        } else {
                            throw new Error(uploadData.error || 'Upload failed');
                        }
                    } catch (error) {
                        console.error('Upload error:', error);
                        showQRMessage('QR code upload failed: ' + (error.message || 'Unknown error'), 'error');
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                        return;
                    }
                }
                
                // Validate that we have a URL
                if (!qrCodeUrl || qrCodeUrl.trim() === '') {
                    showQRMessage('Please upload a QR code image or enter a URL', 'error');
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                    return;
                }
                
                // Save QR code URL
                const response = await fetch(settingsApiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ key: 'payment_qr_code', value: qrCodeUrl })
                });
                
                if (!response.ok) {
                    throw new Error('Save request failed');
                }
                
                const data = await response.json();
                
                if (data.success) {
                    showQRMessage('QR code saved successfully!', 'success');
                    // Update preview
                    qrPreviewImg.src = qrCodeUrl;
                    qrPreview.style.display = 'flex';
                    // Clear file input after successful save
                    qrCodeFile.value = '';
                    // Update URL field with the saved URL
                    qrCodeURL.value = qrCodeUrl;
                } else {
                    throw new Error(data.error || 'Save failed');
                }
            } catch (error) {
                console.error('Error:', error);
                showQRMessage('Error: ' + (error.message || 'Unknown error occurred'), 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
        
        function showQRMessage(message, type) {
            const msgDiv = document.getElementById('qrMessage');
            msgDiv.textContent = message;
            msgDiv.style.display = 'block';
            msgDiv.style.background = type === 'success' ? '#d4edda' : '#f8d7da';
            msgDiv.style.color = type === 'success' ? '#155724' : '#721c24';
            
            setTimeout(() => {
                msgDiv.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>

