<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$apiUrl = '../api/orders.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin Panel</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <div class="page-header">
                    <h1>Orders</h1>
                    <select id="statusFilter" onchange="loadOrders()">
                        <option value="">All Orders</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment Proof</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="orders-table">
                            <tr><td colspan="8">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Screenshot Modal -->
    <div id="screenshotModal" class="modal-overlay" style="display: none;">
        <div class="modal">
            <div class="modal-header">
                <h2>Payment Proof</h2>
                <button class="modal-close" onclick="closeScreenshotModal()">&times;</button>
            </div>
            <div id="screenshotContent"></div>
            <div class="action-buttons" style="margin-top: 20px;" id="screenshotActions">
                <button class="btn btn-success" onclick="updateOrderStatusFromScreenshot('approved')">Approve</button>
                <button class="btn btn-danger" onclick="updateOrderStatusFromScreenshot('rejected')">Reject</button>
            </div>
        </div>
    </div>
    
    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="modal-overlay" style="display: none;">
        <div class="modal" style="max-width: 600px;">
            <div class="modal-header">
                <h2>Order Details</h2>
                <button class="modal-close" onclick="closeOrderDetailsModal()">&times;</button>
            </div>
            <div id="orderDetailsContent" style="padding: 20px;">
                <div style="text-align: center; padding: 20px;">
                    <div class="loading-spinner">Loading...</div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/admin.js"></script>
    <script>
        const apiUrl = '<?php echo $apiUrl; ?>';
        let currentOrderId = null;
        
        // Global escapeHtml function to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                background: ${type === 'success' ? '#d4edda' : '#f8d7da'};
                color: ${type === 'success' ? '#155724' : '#721c24'};
                border-radius: 5px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 10000;
                animation: slideIn 0.3s ease;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
        
        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
        
        function loadOrders() {
            const status = document.getElementById('statusFilter').value;
            const url = status ? `${apiUrl}?status=${status}` : apiUrl;
            
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        displayOrders(data.data);
                    }
                });
        }
        
        function displayOrders(orders) {
            const tbody = document.getElementById('orders-table');
            if (orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8">No orders found</td></tr>';
                return;
            }
            
            tbody.innerHTML = orders.map(order => {
                // Use global escapeHtml function
                const email = escapeHtml(order.customer_email || 'N/A');
                const phone = escapeHtml(order.customer_phone || 'N/A');
                const productTitle = escapeHtml(order.product_title || 'N/A');
                const amount = parseFloat(order.total_amount || 0).toFixed(2);
                const date = new Date(order.created_at).toLocaleDateString();
                const screenshotUrl = order.payment_screenshot ? escapeHtml(order.payment_screenshot) : '';
                
                return `
                <tr>
                    <td>#${order.id}</td>
                    <td>
                        <div><strong>${email}</strong></div>
                        <small style="color: #7f8c8d;">📞 ${phone}</small>
                    </td>
                    <td>${productTitle}</td>
                    <td>₹${amount}</td>
                    <td><span class="status-badge status-${order.status}">${order.status}</span></td>
                    <td>
                        ${screenshotUrl ? 
                            `<button class="btn btn-small" onclick="viewScreenshot(${order.id}, '${screenshotUrl.replace(/'/g, "\\'")}')">📷 View Proof</button>` : 
                            '<span style="color: #7f8c8d;">No proof</span>'
                        }
                    </td>
                    <td>${date}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-primary btn-small" onclick="openOrderDetails(${order.id})">📋 Details</button>
                            ${order.status === 'pending' ? `
                                <button class="btn btn-success btn-small" onclick="updateOrderStatus(${order.id}, 'approved')">✓ Approve</button>
                                <button class="btn btn-danger btn-small" onclick="updateOrderStatus(${order.id}, 'rejected')">✗ Reject</button>
                            ` : ''}
                            <button class="btn btn-danger btn-small" onclick="deleteOrder(${order.id})">Delete</button>
                        </div>
                    </td>
                </tr>
            `;
            }).join('');
        }
        
        function viewScreenshot(orderId, screenshotUrl) {
            if (!orderId) {
                alert('Error: Order ID is required');
                return;
            }
            
            currentOrderId = orderId;
            
            // Store order ID in modal for button access
            const modal = document.getElementById('screenshotModal');
            modal.setAttribute('data-order-id', orderId);
            
            // Handle both base64 data URLs and regular URLs
            const imgHtml = screenshotUrl.startsWith('data:') 
                ? `<img src="${screenshotUrl}" style="max-width: 100%; height: auto; border-radius: 8px;">`
                : `<img src="${screenshotUrl}" style="max-width: 100%; height: auto; border-radius: 8px;" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\'%3EImage not found%3C/text%3E%3C/svg%3E'">`;
            document.getElementById('screenshotContent').innerHTML = `
                <div style="text-align: center; padding: 20px;">
                    ${imgHtml}
                    <p style="margin-top: 15px; color: #666; font-size: 14px;">Payment Screenshot for Order #${orderId}</p>
                </div>
            `;
            document.getElementById('screenshotModal').style.display = 'flex';
        }
        
        function closeScreenshotModal() {
            document.getElementById('screenshotModal').style.display = 'none';
            currentOrderId = null;
            document.getElementById('screenshotModal').removeAttribute('data-order-id');
        }
        
        function updateOrderStatusFromScreenshot(status) {
            // Try to get order ID from multiple sources
            const modal = document.getElementById('screenshotModal');
            const orderId = currentOrderId || modal.getAttribute('data-order-id');
            
            if (!orderId) {
                alert('Error: Order ID is missing. Please close and reopen the payment screenshot.');
                return;
            }
            
            updateOrderStatus(orderId, status);
        }
        
        function updateOrderStatus(orderId, status) {
            // Validate orderId - handle both number and string
            if (!orderId || orderId === 'null' || orderId === 'undefined') {
                alert('Error: Order ID is required');
                console.error('updateOrderStatus called with invalid orderId:', orderId);
                return;
            }
            
            // Convert to number if it's a string
            const numericOrderId = parseInt(orderId, 10);
            if (isNaN(numericOrderId) || numericOrderId <= 0) {
                alert('Error: Invalid Order ID');
                console.error('Invalid orderId format:', orderId);
                return;
            }
            
            if (confirm(`Are you sure you want to ${status} this order?`)) {
                fetch(apiUrl, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: numericOrderId, status: status })
                })
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        if (document.getElementById('screenshotModal').style.display !== 'none') {
                            closeScreenshotModal();
                        }
                        if (document.getElementById('orderDetailsModal').style.display !== 'none') {
                            closeOrderDetailsModal();
                        }
                        loadOrders();
                        if (status === 'approved') {
                            showNotification('Order approved successfully! Product link will be automatically sent to customer.', 'success');
                        } else {
                            showNotification(`Order ${status} successfully!`, 'success');
                        }
                    } else {
                        alert('Error: ' + (data.error || 'Failed to update order'));
                    }
                })
                .catch(error => {
                    console.error('Error updating order status:', error);
                    alert('Error: ' + error.message);
                });
            }
        }
        
        function deleteOrder(orderId) {
            if (!orderId) {
                alert('Error: Order ID is required');
                return;
            }
            
            if (confirm('Are you sure you want to delete this order?')) {
                fetch(`${apiUrl}?id=${orderId}`, { method: 'DELETE' })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            loadOrders();
                        } else {
                            alert('Error: ' + data.error);
                        }
                    });
            }
        }
        
        function openOrderDetails(orderId) {
            currentOrderId = orderId;
            document.getElementById('orderDetailsModal').style.display = 'flex';
            
            // Load order details
            fetch(`${apiUrl}?id=${orderId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        displayOrderDetails(data.data);
                    } else {
                        document.getElementById('orderDetailsContent').innerHTML = 
                            '<div style="color: red; text-align: center; padding: 20px;">Error loading order details</div>';
                    }
                })
                .catch(error => {
                    document.getElementById('orderDetailsContent').innerHTML = 
                        '<div style="color: red; text-align: center; padding: 20px;">Error: ' + error.message + '</div>';
                });
        }
        
        function displayOrderDetails(order) {
            // Use global escapeHtml function
            const email = escapeHtml(order.customer_email || 'N/A');
            const phone = escapeHtml(order.customer_phone || 'N/A');
            const productTitle = escapeHtml(order.product_title || 'N/A');
            const amount = parseFloat(order.total_amount || 0).toFixed(2);
            const date = new Date(order.created_at).toLocaleDateString();
            const status = order.status || 'pending';
            const productLink = escapeHtml(order.product_link || '');
            const notes = escapeHtml(order.notes || '');
            
            document.getElementById('orderDetailsContent').innerHTML = `
                <div style="margin-bottom: 20px;">
                    <h3 style="margin-bottom: 15px; color: #2c3e50;">Order Information</h3>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <div style="margin-bottom: 10px;">
                            <strong>Order ID:</strong> #${order.id}
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Customer Email:</strong> ${email}
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Customer Phone:</strong> ${phone}
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Product:</strong> ${productTitle}
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Amount:</strong> ₹${amount}
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Status:</strong> <span class="status-badge status-${status}">${status}</span>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong>Date:</strong> ${date}
                        </div>
                        ${notes ? `<div style="margin-bottom: 10px;"><strong>Notes:</strong> ${notes}</div>` : ''}
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <h3 style="margin-bottom: 15px; color: #2c3e50;">Product Download Link</h3>
                    ${productLink ? `
                        <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                            <p style="margin: 0 0 10px 0; color: #2e7d32; font-weight: 600;">✓ Link Available</p>
                            <a href="${productLink}" target="_blank" style="color: #1976d2; word-break: break-all; display: block;">${productLink}</a>
                        </div>
                        <div class="form-group">
                            <label>Override Link (Optional)</label>
                            <input type="url" id="productLinkInput" value="${productLink}" 
                                   placeholder="https://drive.google.com/..." 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 10px;">
                            <button class="btn btn-primary" onclick="saveProductLink(${order.id})" style="width: 100%;">
                                💾 Update Product Link
                            </button>
                        </div>
                    ` : `
                        <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                            <p style="margin: 0; color: #856404;">⚠️ No product link found. The link will be automatically fetched from the product settings when you approve this order.</p>
                        </div>
                        <div class="form-group">
                            <label>Manual Override Link (Optional)</label>
                            <input type="url" id="productLinkInput" value="" 
                                   placeholder="https://drive.google.com/..." 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 10px;">
                            <button class="btn btn-primary" onclick="saveProductLink(${order.id})" style="width: 100%;">
                                💾 Save Product Link
                            </button>
                        </div>
                    `}
                </div>
                
                <div style="margin-bottom: 20px;">
                    <h3 style="margin-bottom: 15px; color: #2c3e50;">Actions</h3>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        ${status === 'pending' ? `
                            <button class="btn btn-success" onclick="updateOrderStatus(${order.id}, 'approved')" style="flex: 1;">
                                ✅ Approve Order (Auto-send link)
                            </button>
                        ` : ''}
                        ${productLink ? `
                            <button class="btn btn-primary" onclick="sendProductLinkWithEvent(${order.id}, '${email.replace(/'/g, "\\'")}', event)" style="flex: 1;">
                                📧 Re-send Link via Email
                            </button>
                        ` : ''}
                        ${order.payment_screenshot ? `
                            <button class="btn btn-small" onclick="viewScreenshot(${order.id}, '${escapeHtml(order.payment_screenshot).replace(/'/g, "\\'")}')" style="flex: 1;">
                                📷 View Payment Proof
                            </button>
                        ` : ''}
                    </div>
                    ${status === 'pending' ? `
                        <p style="margin-top: 10px; color: #666; font-size: 13px;">
                            ℹ️ When you approve this order, the product download link will be automatically sent to the customer's email address.
                        </p>
                    ` : ''}
                </div>
            `;
        }
        
        function closeOrderDetailsModal() {
            document.getElementById('orderDetailsModal').style.display = 'none';
            currentOrderId = null;
        }
        
        function saveProductLink(orderId) {
            if (!orderId) {
                alert('Error: Order ID is required');
                return;
            }
            
            const productLink = document.getElementById('productLinkInput').value.trim();
            
            if (!productLink) {
                alert('Please enter a product link');
                return;
            }
            
            fetch(apiUrl, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: orderId, product_link: productLink })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotification('Product link saved successfully!', 'success');
                    // Reload order details
                    openOrderDetails(orderId);
                    loadOrders();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }
        
        // This function is no longer needed - approval now automatically sends email
        // Keeping for backward compatibility but not used in UI
        
        function sendProductLinkWithEvent(orderId, customerEmail, event) {
            sendProductLink(orderId, customerEmail, false, event);
        }
        
        function sendProductLink(orderId, customerEmail, autoVerify = false, event = null) {
            if (!orderId) {
                alert('Error: Order ID is required');
                return;
            }
            
            if (!customerEmail) {
                alert('Error: Customer email is required');
                return;
            }
            
            // Show loading state if button was clicked
            let sendButton = null;
            let originalText = '📧 Send Link via Email';
            
            if (event && event.target) {
                sendButton = event.target;
                originalText = sendButton.textContent;
                sendButton.disabled = true;
                sendButton.textContent = 'Loading...';
            }
            
            // Get order details first
            fetch(`${apiUrl}?id=${orderId}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    if (!data.success || !data.data) {
                        if (sendButton) {
                            sendButton.disabled = false;
                            sendButton.textContent = originalText;
                        }
                        alert('Error loading order details: ' + (data.error || 'Order not found'));
                        return;
                    }
                    
                    const order = data.data;
                    const productLink = order.product_link;
                    
                    if (!productLink) {
                        if (sendButton) {
                            sendButton.disabled = false;
                            sendButton.textContent = originalText;
                        }
                        alert('Please add a product link first');
                        return;
                    }
                    
                    if (sendButton) {
                        sendButton.textContent = 'Sending Email...';
                    }
                    
                    // Prepare email
                    // Escape HTML for email content
                    const safeProductTitle = escapeHtml(order.product_title || 'Digital Product');
                    const safeProductLink = escapeHtml(productLink);
                    
                    // Build email message
                    const emailMessage = '<html><head><style>body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; } .container { max-width: 600px; margin: 0 auto; padding: 20px; } .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; } .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; } .button { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; } .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }</style></head><body><div class="container"><div class="header"><h1>🎉 Payment Verified!</h1></div><div class="content"><p>Dear Customer,</p><p>Thank you for your purchase! Your payment has been verified.</p><p><strong>Order ID:</strong> #' + order.id + '</p><p><strong>Product:</strong> ' + safeProductTitle + '</p><p>Click the button below to download your product:</p><div style="text-align: center;"><a href="' + safeProductLink + '" class="button" target="_blank">Download Product</a></div><p>Or copy this link:</p><p style="word-break: break-all; background: #fff; padding: 10px; border-radius: 5px;">' + safeProductLink + '</p><p>If you have any questions, please contact us.</p><p>Best regards,<br>Hamro Digi Cart Team</p></div><div class="footer"><p>This is an automated email. Please do not reply.</p></div></div></body></html>';
                    
                    const emailData = {
                        to: customerEmail.trim(),
                        subject: 'Your Product Download Link - Hamro Digi Cart',
                        message: emailMessage
                    };
                    
                    // Validate email data before sending
                    if (!emailData.to || !emailData.subject || !emailData.message) {
                        if (sendButton) {
                            sendButton.disabled = false;
                            sendButton.textContent = originalText;
                        }
                        alert('Error: Email data is incomplete. Please try again.');
                        return;
                    }
                    
                    // Send email
                    fetch('../api/send-email.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(emailData)
                    })
                    .then(async res => {
                        // Always try to parse JSON response first
                        let responseData;
                        try {
                            const responseText = await res.text();
                            if (responseText) {
                                responseData = JSON.parse(responseText);
                            } else {
                                responseData = { success: false, error: 'Empty response from server' };
                            }
                        } catch (e) {
                            // If response is not JSON, create error object
                            responseData = { 
                                success: false, 
                                error: res.statusText || `HTTP error! status: ${res.status}` 
                            };
                        }
                        
                        if (!res.ok) {
                            const errorMsg = responseData.error || responseData.message || `HTTP error! status: ${res.status}`;
                            throw new Error(errorMsg);
                        }
                        
                        return responseData;
                    })
                    .then(emailResult => {
                        if (emailResult && emailResult.success) {
                            showNotification('✅ Product link sent to ' + customerEmail + ' successfully!', 'success');
                            if (autoVerify) {
                                loadOrders();
                                closeOrderDetailsModal();
                            } else {
                                // Reload order details to show updated status
                                openOrderDetails(orderId);
                            }
                        } else {
                            const errorMsg = emailResult?.error || emailResult?.message || 'Unknown error. Please check SMTP settings.';
                            alert('Error sending email: ' + errorMsg);
                        }
                    })
                    .catch(error => {
                        console.error('Email sending error:', error);
                        
                        // Extract error message properly
                        let errorMsg = 'Unknown error';
                        if (error && typeof error === 'object') {
                            if (error.message) {
                                errorMsg = error.message;
                            } else if (error.error) {
                                errorMsg = error.error;
                            } else if (typeof error === 'string') {
                                errorMsg = error;
                            }
                        } else if (typeof error === 'string') {
                            errorMsg = error;
                        }
                        
                        // Don't show "true" or "false" as error messages
                        if (errorMsg === 'true' || errorMsg === 'false' || errorMsg === true || errorMsg === false) {
                            errorMsg = 'Email sending failed. Please check SMTP configuration.';
                        }
                        
                        // Provide specific guidance based on error
                        if (errorMsg.includes('401') || errorMsg.includes('Authentication') || errorMsg.includes('Unauthorized')) {
                            errorMsg += '\n\nPlease refresh the page and log in again.';
                        } else if (errorMsg.includes('400') || errorMsg.includes('required') || errorMsg.includes('Invalid')) {
                            errorMsg += '\n\nPlease check that all email fields are filled correctly.';
                        } else if (errorMsg.includes('SMTP') || errorMsg.includes('configured') || errorMsg.includes('not configured')) {
                            errorMsg += '\n\nPlease configure SMTP settings in Admin Panel → Settings → SMTP Email Settings.';
                        } else if (!errorMsg.includes('Please check')) {
                            errorMsg += '\n\nPlease check:\n1. SMTP settings are configured\n2. SMTP credentials are correct\n3. Internet connection is active';
                        }
                        
                        alert('Error sending email: ' + errorMsg);
                    })
                    .finally(() => {
                        if (sendButton) {
                            sendButton.disabled = false;
                            sendButton.textContent = originalText;
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading order:', error);
                    alert('Error loading order details: ' + error.message);
                    if (sendButton) {
                        sendButton.disabled = false;
                        sendButton.textContent = originalText;
                    }
                });
        }
        
        document.getElementById('screenshotModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeScreenshotModal();
            }
        });
        
        document.getElementById('orderDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOrderDetailsModal();
            }
        });
        
        loadOrders();
    </script>
</body>
</html>

