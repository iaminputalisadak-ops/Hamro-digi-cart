<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$pdo = getDBConnection();
$message = '';
$error = '';

// Get all orders to show customer emails
$stmt = $pdo->query("SELECT DISTINCT customer_email, customer_name FROM orders WHERE customer_email IS NOT NULL AND customer_email != '' ORDER BY created_at DESC LIMIT 100");
$customers = $stmt->fetchAll();

// Get recent sent emails (if we track them)
$recentEmails = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email - Admin Panel</title>
    <?php include 'includes/favicon.php'; ?>
    <link rel="stylesheet" href="assets/admin.css">
    <style>
        .email-form-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }
        .email-form-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-preview {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            min-height: 400px;
        }
        .customer-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 10px;
            margin-top: 10px;
        }
        .customer-item {
            padding: 8px;
            cursor: pointer;
            border-radius: 4px;
            margin-bottom: 5px;
        }
        .customer-item:hover {
            background: #f3f4f6;
        }
        .email-templates {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .template-btn {
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            background: white;
            cursor: pointer;
            text-align: center;
        }
        .template-btn:hover {
            background: #f3f4f6;
            border-color: #6366f1;
        }
        @media (max-width: 968px) {
            .email-form-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <h1>📧 Send Email</h1>
                <p style="color: #666; margin-bottom: 20px;">Send emails to customers or any email address.</p>
                
                <div id="emailStatusMessage" style="display: none; padding: 15px; margin: 20px 0; border-radius: 5px;"></div>
                
                <div class="email-form-container">
                    <!-- Email Form -->
                    <div class="email-form-section">
                        <h2 style="margin-bottom: 20px;">Compose Email</h2>
                        
                        <form id="emailForm">
                            <div class="form-group">
                                <label>To (Email Address)</label>
                                <input type="email" id="emailTo" placeholder="customer@example.com" required>
                                <small style="color: #666; display: block; margin-top: 5px;">
                                    Or select from recent customers:
                                </small>
                                <div class="customer-list">
                                    <?php if (count($customers) > 0): ?>
                                        <?php foreach ($customers as $customer): ?>
                                            <div class="customer-item" onclick="selectCustomer('<?php echo htmlspecialchars($customer['customer_email']); ?>', '<?php echo htmlspecialchars($customer['customer_name'] ?? ''); ?>')">
                                                <strong><?php echo htmlspecialchars($customer['customer_email']); ?></strong>
                                                <?php if (!empty($customer['customer_name'])): ?>
                                                    <br><small style="color: #666;"><?php echo htmlspecialchars($customer['customer_name']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p style="color: #666; text-align: center; padding: 20px;">No customers found</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" id="emailSubject" placeholder="Email subject" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Email Templates (Quick Fill)</label>
                                <div class="email-templates">
                                    <div class="template-btn" onclick="loadTemplate('product-link')">Product Link</div>
                                    <div class="template-btn" onclick="loadTemplate('welcome')">Welcome</div>
                                    <div class="template-btn" onclick="loadTemplate('promotion')">Promotion</div>
                                    <div class="template-btn" onclick="loadTemplate('custom')">Custom</div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Message (HTML Supported)</label>
                                <textarea id="emailBody" rows="12" placeholder="Enter your email message here. HTML is supported." required></textarea>
                                <small style="color: #666;">You can use HTML tags for formatting</small>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" id="sendCopy" value="1">
                                    Send a copy to admin email
                                </label>
                            </div>
                            
                            <div class="action-buttons">
                                <button type="submit" class="btn btn-primary">📤 Send Email</button>
                                <button type="button" class="btn btn-secondary" onclick="previewEmail()">👁️ Preview</button>
                                <button type="button" class="btn btn-secondary" onclick="clearForm()">🗑️ Clear</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Email Preview -->
                    <div class="email-form-section">
                        <h2 style="margin-bottom: 20px;">Email Preview</h2>
                        <div class="email-preview" id="emailPreview">
                            <p style="color: #999; text-align: center; padding: 50px;">Email preview will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/admin.js"></script>
    <script>
        const emailTemplates = {
            'product-link': {
                subject: 'Your Product Download Link - Hamro Digi Cart',
                message: `<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
                        <h1 style="margin: 0;">🎉 Payment Verified!</h1>
                    </div>
                    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px;">
                        <p>Dear Customer,</p>
                        <p>Thank you for your purchase! Your payment has been verified.</p>
                        <p><strong>Order ID:</strong> #ORDER_ID</p>
                        <p><strong>Product:</strong> PRODUCT_NAME</p>
                        <p>Click the button below to download your product:</p>
                        <div style="text-align: center; margin: 20px 0;">
                            <a href="PRODUCT_LINK" style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 5px;">Download Product</a>
                        </div>
                        <p>Or copy this link:</p>
                        <p style="word-break: break-all; background: #fff; padding: 10px; border-radius: 5px;">PRODUCT_LINK</p>
                        <p>If you have any questions, please contact us.</p>
                        <p>Best regards,<br>Hamro Digi Cart Team</p>
                    </div>
                </div>`
            },
            'welcome': {
                subject: 'Welcome to Hamro Digi Cart!',
                message: `<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
                        <h1 style="margin: 0;">Welcome!</h1>
                    </div>
                    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px;">
                        <p>Dear Customer,</p>
                        <p>Thank you for choosing Hamro Digi Cart! We're excited to have you as part of our community.</p>
                        <p>We offer the best digital products including:</p>
                        <ul>
                            <li>Premium Reels Bundles</li>
                            <li>WhatsApp Templates</li>
                            <li>Digital Planners</li>
                            <li>Social Media Packs</li>
                        </ul>
                        <p>Browse our collection and find amazing digital products to enhance your content creation!</p>
                        <p>Best regards,<br>Hamro Digi Cart Team</p>
                    </div>
                </div>`
            },
            'promotion': {
                subject: 'Special Offer - Limited Time Only!',
                message: `<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                    <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
                        <h1 style="margin: 0;">🔥 Special Offer!</h1>
                    </div>
                    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px;">
                        <p>Dear Customer,</p>
                        <p>We have an exclusive offer just for you!</p>
                        <p style="font-size: 24px; font-weight: bold; color: #d97706; text-align: center; margin: 20px 0;">UP TO 50% OFF</p>
                        <p>Don't miss out on our limited-time promotion. Get premium digital products at unbeatable prices!</p>
                        <div style="text-align: center; margin: 20px 0;">
                            <a href="https://yourwebsite.com" style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; text-decoration: none; border-radius: 5px;">Shop Now</a>
                        </div>
                        <p>Offer valid for a limited time only!</p>
                        <p>Best regards,<br>Hamro Digi Cart Team</p>
                    </div>
                </div>`
            },
            'custom': {
                subject: '',
                message: ''
            }
        };
        
        function selectCustomer(email, name) {
            document.getElementById('emailTo').value = email;
            if (name) {
                document.getElementById('emailSubject').value = `Hello ${name},`;
            }
        }
        
        function loadTemplate(templateName) {
            const template = emailTemplates[templateName];
            if (template) {
                if (template.subject) {
                    document.getElementById('emailSubject').value = template.subject;
                }
                if (template.message) {
                    document.getElementById('emailBody').value = template.message;
                }
                previewEmail();
            }
        }
        
        function previewEmail() {
            const subject = document.getElementById('emailSubject').value;
            const message = document.getElementById('emailBody').value;
            const preview = document.getElementById('emailPreview');
            
            if (!message) {
                preview.innerHTML = '<p style="color: #999; text-align: center; padding: 50px;">Enter a message to see preview</p>';
                return;
            }
            
            preview.innerHTML = `
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; background: white;">
                    <div style="border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 20px;">
                        <strong>To:</strong> <span id="previewTo">${document.getElementById('emailTo').value || 'customer@example.com'}</span><br>
                        <strong>Subject:</strong> ${subject || '(No subject)'}
                    </div>
                    <div>${message}</div>
                </div>
            `;
        }
        
        function clearForm() {
            if (confirm('Clear all form fields?')) {
                document.getElementById('emailForm').reset();
                document.getElementById('emailPreview').innerHTML = '<p style="color: #999; text-align: center; padding: 50px;">Email preview will appear here</p>';
            }
        }
        
        // Auto-preview on input
        document.getElementById('emailSubject').addEventListener('input', previewEmail);
        document.getElementById('emailBody').addEventListener('input', previewEmail);
        document.getElementById('emailTo').addEventListener('input', function() {
            const preview = document.getElementById('emailPreview');
            const previewTo = preview.querySelector('#previewTo');
            if (previewTo) {
                previewTo.textContent = this.value || 'customer@example.com';
            }
        });
        
        // Handle form submission
        document.getElementById('emailForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const to = document.getElementById('emailTo').value;
            const subject = document.getElementById('emailSubject').value;
            const message = document.getElementById('emailBody').value;
            const sendCopy = document.getElementById('sendCopy').checked;
            
            if (!to || !subject || !message) {
                showMessage('Please fill in all required fields', 'error');
                return;
            }
            
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Sending...';
            
            try {
                // Send main email
                const response = await fetch('../api/send-email.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        to: to,
                        subject: subject,
                        message: message
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Send copy if requested
                    if (sendCopy) {
                        try {
                            // Get admin email from settings
                            const settingsResponse = await fetch('../api/settings.php');
                            const settingsData = await settingsResponse.json();
                            const adminEmail = settingsData.success && settingsData.data.smtp_email ? settingsData.data.smtp_email : null;
                            
                            if (adminEmail) {
                                await fetch('../api/send-email.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({
                                        to: adminEmail,
                                        subject: 'Copy: ' + subject,
                                        message: `<p><strong>Copy of email sent to:</strong> ${to}</p><hr>${message}`
                                    })
                                });
                            }
                        } catch (copyError) {
                            console.error('Error sending copy:', copyError);
                        }
                    }
                    
                    showMessage('Email sent successfully to ' + to + '!', 'success');
                    clearForm();
                } else {
                    showMessage('Error: ' + (data.error || 'Failed to send email'), 'error');
                }
            } catch (error) {
                showMessage('Error: ' + error.message, 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
        
        function showMessage(msg, type) {
            const msgDiv = document.getElementById('emailStatusMessage');
            msgDiv.textContent = msg;
            msgDiv.style.display = 'block';
            msgDiv.style.background = type === 'success' ? '#d4edda' : '#f8d7da';
            msgDiv.style.color = type === 'success' ? '#155724' : '#721c24';
            
            setTimeout(() => {
                msgDiv.style.display = 'none';
            }, 5000);
        }
        
        // Initial preview
        previewEmail();
    </script>
</body>
</html>

