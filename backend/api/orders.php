<?php
/**
 * Orders API
 */

// Suppress error display for clean JSON output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/smtp.php';

// Make sure getSMTPSettings is available
if (!function_exists('getSMTPSettings')) {
    function getSMTPSettings() {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'smtp_%'");
        $settings = $stmt->fetchAll();
        
        $smtpSettings = [];
        foreach ($settings as $setting) {
            $smtpSettings[$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $smtpSettings;
    }
}

/**
 * Send purchase confirmation email to customer when order is created
 * Moved here to ensure it's available before use
 */
function sendPurchaseConfirmationEmail($order) {
    try {
        // Check if SMTP is configured
        $smtpSettings = getSMTPSettings();
        if (empty($smtpSettings['smtp_host']) || empty($smtpSettings['smtp_email']) || empty($smtpSettings['smtp_password'])) {
            error_log("SMTP not configured - cannot send purchase confirmation email for order #" . ($order['id'] ?? 'unknown'));
            return ['success' => false, 'error' => 'SMTP not configured'];
        }
        
        $orderId = $order['id'];
        $customerName = htmlspecialchars($order['customer_name'] ?? 'Customer');
        $customerEmail = htmlspecialchars($order['customer_email']);
        $productTitle = htmlspecialchars($order['product_title'] ?? 'Digital Product');
        $totalAmount = number_format($order['total_amount'], 2);
        $orderDate = date('F j, Y, g:i a', strtotime($order['created_at']));
        
        // Get base URL
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $protocol . '://' . $host;
        
        // Build email message
        $emailSubject = "🛒 Order Confirmation - Order #{$orderId}";
        
        $emailMessage = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px 20px; background: #f9fafb; }
        .order-box { background: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #6b7280; }
        .info-value { color: #1f2937; text-align: right; }
        .alert { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; background: #f9fafb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛒 Order Confirmation</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Order #' . $orderId . '</p>
        </div>
        <div class="content">
            <p>Dear ' . $customerName . ',</p>
            <p>Thank you for your purchase! We have received your order and payment details.</p>
            
            <div class="alert">
                <strong>⏳ Payment Verification:</strong> Your payment is being reviewed. Once verified (usually within 30 minutes), you will receive another email with your product download link.
            </div>
            
            <div class="order-box">
                <h2 style="margin-top: 0; color: #1f2937;">Order Details</h2>
                <div class="info-row">
                    <span class="info-label">Order ID:</span>
                    <span class="info-value"><strong>#' . $orderId . '</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Date:</span>
                    <span class="info-value">' . $orderDate . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Product:</span>
                    <span class="info-value"><strong>' . $productTitle . '</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Amount:</span>
                    <span class="info-value" style="color: #22c55e; font-weight: 700; font-size: 18px;">रु' . $totalAmount . '</span>
                </div>
            </div>
            
            <div style="background: #f0fdf4; border-left: 4px solid #22c55e; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <strong>📋 What Happens Next?</strong>
                <ol style="margin: 10px 0; padding-left: 20px;">
                    <li>Our team will review your payment screenshot</li>
                    <li>Once verified, your order will be approved</li>
                    <li>You will receive an email with the product download link</li>
                    <li>Download and enjoy your purchase!</li>
                </ol>
            </div>
            
            <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>
            <p>Best regards,<br><strong>Hamro Digi Cart Team</strong></p>
        </div>
        <div class="footer">
            <p>This is an automated confirmation email for your order.</p>
            <p>Order Management System</p>
        </div>
    </div>
</body>
</html>';
        
        error_log("Sending purchase confirmation email to customer: " . $customerEmail . " for order #" . $orderId);
        
        $result = sendSMTPEmail(
            $customerEmail,
            $emailSubject,
            $emailMessage,
            $smtpSettings['smtp_email'] ?? null,
            $smtpSettings['smtp_from_name'] ?? null
        );
        
        if (!$result['success']) {
            $errorMsg = "Failed to send purchase confirmation email for order #" . $orderId . ": " . ($result['error'] ?? 'Unknown error');
            error_log($errorMsg);
            return ['success' => false, 'error' => $result['error'] ?? 'Unknown error'];
        }
        
        error_log("Purchase confirmation email sent successfully to " . $customerEmail . " for order #" . $orderId);
        return ['success' => true, 'message' => 'Email sent successfully', 'to' => $customerEmail];
    } catch (Exception $e) {
        $errorMsg = "Exception sending purchase confirmation email for order #" . ($order['id'] ?? 'unknown') . ": " . $e->getMessage();
        error_log($errorMsg);
        error_log("Exception trace: " . $e->getTraceAsString());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Send email notification to admin when new order is created
 * Moved here to ensure it's available before use
 */
function sendNewOrderNotificationToAdmin($order) {
    try {
        // Get admin email from settings - prefer admin_order_notification_email, then smtp_email, then admins.email, then contact_email.
        // If any candidate is invalid, skip it and try the next one.
        $pdo = getDBConnection();

        $candidates = [];

        // 1) Dedicated admin notification email
        try {
            $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'admin_order_notification_email'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && !empty($result['setting_value'])) $candidates[] = trim($result['setting_value']);
        } catch (Throwable $e) {
            // ignore
        }

        // 2) SMTP email (From Email) - send admin notifications "to me" by default if dedicated admin email isn't set
        try {
            $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'smtp_email'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && !empty($result['setting_value'])) $candidates[] = trim($result['setting_value']);
        } catch (Throwable $e) {
            // ignore
        }

        // 3) Admin users table email
        try {
            $stmt = $pdo->query("SELECT email FROM admins WHERE email IS NOT NULL AND email <> '' ORDER BY id ASC LIMIT 1");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['email'])) $candidates[] = trim($row['email']);
        } catch (Throwable $e) {
            // ignore
        }

        // 4) Contact email (last fallback)
        try {
            $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'contact_email'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && !empty($result['setting_value'])) $candidates[] = trim($result['setting_value']);
        } catch (Throwable $e) {
            // ignore
        }

        $adminEmail = '';
        foreach ($candidates as $cand) {
            if (is_string($cand) && $cand !== '' && filter_var($cand, FILTER_VALIDATE_EMAIL)) {
                $adminEmail = $cand;
                break;
            }
        }

        if (empty($adminEmail)) {
            $errorMsg = "Admin email not configured. Please set admin_order_notification_email in Website Settings, or configure SMTP Email (From Email) in Settings.";
            error_log($errorMsg);
            return ['success' => false, 'error' => $errorMsg];
        }

        error_log("Sending admin notification email to: " . $adminEmail . " for order #" . ($order['id'] ?? 'unknown'));
        
        $orderId = $order['id'];
        $customerName = htmlspecialchars($order['customer_name'] ?? 'Not provided');
        $customerEmail = htmlspecialchars($order['customer_email']);
        $customerPhone = htmlspecialchars($order['customer_phone'] ?? 'Not provided');
        $productTitle = htmlspecialchars($order['product_title'] ?? 'Unknown Product');
        $totalAmount = number_format($order['total_amount'], 2);
        $paymentScreenshot = $order['payment_screenshot'] ?? '';
        // Payment method isn't stored explicitly; show the current flow used on frontend.
        $paymentMethod = 'QR Payment (Banking/Esewa/Khalti)';
        $orderDate = date('F j, Y, g:i a', strtotime($order['created_at']));
        
        // Get base URL for payment screenshot
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $protocol . '://' . $host;

        // Link to view the order in admin panel
        $viewOrderLink = $baseUrl . '/admin/orders.php?order_id=' . urlencode($orderId);
        
        // Build email message
        $emailSubject = "New Order Received - Order #{$orderId}";
        
        $screenshotHtml = '';
        if (!empty($paymentScreenshot)) {
            $screenshotUrl = strpos($paymentScreenshot, 'http') === 0 ? $paymentScreenshot : $baseUrl . $paymentScreenshot;
            $screenshotHtml = '
                <div style="margin: 20px 0;">
                    <h3 style="color: #333; margin-bottom: 10px;">Payment Screenshot:</h3>
                    <a href="' . htmlspecialchars($screenshotUrl) . '" target="_blank" style="display: inline-block; padding: 10px 20px; background: #22c55e; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0;">View Payment Screenshot</a>
                    <br>
                    <img src="' . htmlspecialchars($screenshotUrl) . '" alt="Payment Screenshot" style="max-width: 100%; border: 2px solid #e5e7eb; border-radius: 8px; margin-top: 10px;">
                </div>';
        }
        
        $emailMessage = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px 20px; background: #f9fafb; }
        .order-box { background: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #6b7280; }
        .info-value { color: #1f2937; text-align: right; }
        .button { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; background: #f9fafb; }
        .alert { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛒 New Order Received!</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Order #' . $orderId . '</p>
        </div>
        <div class="content">
            <div class="alert">
                <strong>⚠️ Action Required:</strong> A customer has submitted a payment. Please review the payment screenshot and approve/reject the order in the admin panel.
            </div>
            
            <div class="order-box">
                <h2 style="margin-top: 0; color: #1f2937;">Order Details</h2>
                <div class="info-row">
                    <span class="info-label">Order ID:</span>
                    <span class="info-value"><strong>#' . $orderId . '</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Date:</span>
                    <span class="info-value">' . $orderDate . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Product:</span>
                    <span class="info-value"><strong>' . $productTitle . '</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Amount:</span>
                    <span class="info-value" style="color: #22c55e; font-weight: 700; font-size: 18px;">₹' . $totalAmount . '</span>
                </div>
            </div>
            
            <div class="order-box">
                <h2 style="margin-top: 0; color: #1f2937;">Customer Information</h2>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">' . $customerName . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><a href="mailto:' . $customerEmail . '" style="color: #22c55e;">' . $customerEmail . '</a></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">' . $customerPhone . '</span>
                </div>
            </div>
            
            ' . $screenshotHtml . '
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="' . htmlspecialchars($viewOrderLink) . '" class="button" target="_blank">View Order in Admin Panel</a>
            </div>
            
            <div style="background: #f0fdf4; border-left: 4px solid #22c55e; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <strong>📋 Next Steps:</strong>
                <ol style="margin: 10px 0; padding-left: 20px;">
                    <li>Review the payment screenshot above</li>
                    <li>Verify the payment amount matches the order total</li>
                    <li>Go to Admin Panel → Orders</li>
                    <li>Approve the order if payment is verified</li>
                    <li>The customer will automatically receive the download link</li>
                </ol>
            </div>
        </div>
        <div class="footer">
            <p>This is an automated notification from Hamro Digi Cart</p>
            <p>Order Management System</p>
        </div>
    </div>
</body>
</html>';
        
        // Check if SMTP is configured before attempting to send
        $smtpSettings = getSMTPSettings();
        if (empty($smtpSettings['smtp_host']) || empty($smtpSettings['smtp_email']) || empty($smtpSettings['smtp_password'])) {
            $errorMsg = "SMTP not configured. Please configure SMTP settings in Admin Panel → Settings.";
            error_log($errorMsg . " Order #" . $orderId);
            return ['success' => false, 'error' => $errorMsg];
        }
        
        error_log("Attempting to send email to admin: " . $adminEmail . " for order #" . $orderId);
        
        $result = sendSMTPEmail(
            $adminEmail,
            $emailSubject,
            $emailMessage,
            $smtpSettings['smtp_email'] ?? null,
            $smtpSettings['smtp_from_name'] ?? null
        );
        
        // One retry for transient SMTP issues
        if (!$result['success']) {
            error_log("Admin notification email first attempt FAILED for order #" . $orderId . ": " . ($result['error'] ?? 'Unknown error') . " - retrying once...");
            @sleep(1);
            $result = sendSMTPEmail(
                $adminEmail,
                $emailSubject,
                $emailMessage,
                $smtpSettings['smtp_email'] ?? null,
                $smtpSettings['smtp_from_name'] ?? null
            );
        }

        if (!$result['success']) {
            $errorMsg = "Failed to send admin notification email for order #" . $orderId . ": " . ($result['error'] ?? 'Unknown error');
            error_log($errorMsg);
            error_log("SMTP Settings check - Host: " . ($smtpSettings['smtp_host'] ?? 'not set') . ", Email: " . ($smtpSettings['smtp_email'] ?? 'not set'));
            return ['success' => false, 'error' => $result['error'] ?? 'Unknown error'];
        }
        
        error_log("Admin notification email sent successfully to " . $adminEmail . " for order #" . $orderId);
        return ['success' => true, 'message' => 'Email sent successfully', 'to' => $adminEmail];
    } catch (Exception $e) {
        $errorMsg = "Exception sending admin notification email for order #" . ($order['id'] ?? 'unknown') . ": " . $e->getMessage();
        error_log($errorMsg);
        error_log("Exception trace: " . $e->getTraceAsString());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

function ensureOrdersApprovedAtColumnExists($pdo) {
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'approved_at'");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['Field'])) return true;
        // Try to add it (safe to run once; if it already exists, catch)
        $pdo->exec("ALTER TABLE orders ADD COLUMN approved_at DATETIME NULL");
        $pdo->exec("ALTER TABLE orders ADD INDEX idx_approved_at (approved_at)");
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * Treat URLs that look like images as invalid download links (common mistake: saving payment screenshot URL as product_link).
 */
function isLikelyImageUrl($url) {
    if (!is_string($url)) return false;
    $u = strtolower(trim($url));
    if ($u === '') return false;
    // Strip query/hash for extension check
    $u2 = preg_replace('/[?#].*$/', '', $u);
    return (bool)preg_match('/\.(jpg|jpeg|png|gif|webp|svg|ico)$/i', $u2);
}

/**
 * Normalize the product download link: pick the best candidate and ensure absolute URL if it starts with "/".
 */
function normalizeDownloadLink($primary, $fallback, $baseUrl) {
    $primary = is_string($primary) ? trim($primary) : '';
    $fallback = is_string($fallback) ? trim($fallback) : '';

    // If primary looks like an image URL, ignore it and prefer fallback.
    if ($primary !== '' && isLikelyImageUrl($primary)) $primary = '';
    // If fallback looks like an image URL, ignore it too.
    if ($fallback !== '' && isLikelyImageUrl($fallback)) $fallback = '';

    $chosen = $primary !== '' ? $primary : $fallback;
    if ($chosen !== '' && is_string($baseUrl) && $baseUrl !== '' && strpos($chosen, '/') === 0) {
        return rtrim($baseUrl, '/') . $chosen;
    }
    return $chosen;
}

/**
 * Resolve a stored URL/path that points to an uploaded file into a safe local filesystem path.
 * Only allows deletions inside backend/uploads/.
 */
function resolveLocalUploadPath($value) {
    if (empty($value) || !is_string($value)) return null;
    $v = trim($value);

    // Ignore base64 data URLs
    if (strpos($v, 'data:image/') === 0) return null;

    $path = null;
    // If it's a URL, parse it
    $parsed = @parse_url($v);
    if (is_array($parsed) && isset($parsed['path'])) {
        $path = $parsed['path'];
    } else {
        // Might be a relative path already
        $path = $v;
    }

    if (!$path || !is_string($path)) return null;

    // Only handle our uploads paths
    if (strpos($path, '/uploads/') === false && strpos($path, '/backend/uploads/') === false) {
        return null;
    }

    // Extract filename safely
    $filename = basename($path);
    if (empty($filename) || $filename === '.' || $filename === '..') return null;

    $uploadsDir = realpath(__DIR__ . '/../uploads');
    if (!$uploadsDir) return null;

    $full = $uploadsDir . DIRECTORY_SEPARATOR . $filename;

    // Ensure the resolved path stays within uploadsDir
    $fullReal = realpath($full);
    if ($fullReal === false) {
        // File might not exist anymore; return intended safe path (still within uploadsDir)
        return $full;
    }
    if (strpos($fullReal, $uploadsDir) !== 0) return null;
    return $fullReal;
}

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT o.*, p.title as product_title, p.image as product_image, p.product_link as product_download_link 
                                   FROM orders o 
                                   LEFT JOIN products p ON o.product_id = p.id 
                                   WHERE o.id = ?");
            $stmt->execute([$_GET['id']]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($order) {
                // Build base URL (for making relative links absolute)
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? '';
                $baseUrl = $host ? ($protocol . '://' . $host) : '';

                // Prefer a valid download link; ignore image URLs accidentally saved as product_link.
                $order['product_link'] = normalizeDownloadLink(
                    $order['product_link'] ?? '',
                    $order['product_download_link'] ?? '',
                    $baseUrl
                );
                sendSuccess($order);
            } else {
                sendError('Order not found', 404);
            }
        } else {
            $status = isset($_GET['status']) ? $_GET['status'] : null;
            
            $sql = "SELECT o.*, p.title as product_title, p.image as product_image 
                    FROM orders o 
                    LEFT JOIN products p ON o.product_id = p.id";
            
            $params = [];
            if ($status) {
                $sql .= " WHERE o.status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY o.created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $orders = $stmt->fetchAll();
            
            sendSuccess($orders);
        }
        break;
        
    case 'POST':
        ob_clean(); // Clean any output before processing
        
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            ob_end_clean();
            sendError('Invalid JSON data: ' . json_last_error_msg());
            exit;
        }
        
        if (!isset($data['customer_email']) || empty($data['customer_email'])) {
            ob_end_clean();
            sendError('Customer email is required');
            exit;
        }
        
        if (!isset($data['product_id']) || empty($data['product_id'])) {
            ob_end_clean();
            sendError('Product ID is required');
            exit;
        }
        
        // Handle payment screenshot - convert base64 to file if needed
        $paymentScreenshot = '';
        if (!empty($data['payment_screenshot'])) {
            // Check if it's a base64 data URL
            if (preg_match('/^data:image\/(\w+);base64,/', $data['payment_screenshot'], $matches)) {
                $imageType = $matches[1];
                $imageData = base64_decode(substr($data['payment_screenshot'], strpos($data['payment_screenshot'], ',') + 1));
                
                // Create uploads directory if it doesn't exist
                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Generate unique filename
                $filename = 'payment_' . uniqid() . '_' . time() . '.' . $imageType;
                $filepath = $uploadDir . $filename;
                
                // Save the image file
                if (file_put_contents($filepath, $imageData)) {
                    // Get base URL
                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                    $host = $_SERVER['HTTP_HOST'];
                    $baseUrl = $protocol . '://' . $host;
                    
                    // Determine correct uploads path
                    $scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
                    if (strpos($scriptPath, '/backend/') !== false) {
                        $paymentScreenshot = $baseUrl . '/backend/uploads/' . $filename;
                    } else {
                        $paymentScreenshot = $baseUrl . '/uploads/' . $filename;
                    }
                } else {
                    // If file save fails, store base64 as fallback
                    $paymentScreenshot = $data['payment_screenshot'];
                }
            } else {
                // Already a URL, use as-is
                $paymentScreenshot = $data['payment_screenshot'];
            }
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO orders 
                                   (product_id, customer_name, customer_email, customer_phone, 
                                    total_amount, payment_screenshot, status, notes) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['product_id'],
                $data['customer_name'] ?? '',
                $data['customer_email'],
                $data['customer_phone'] ?? '',
                $data['total_amount'] ?? 0,
                $paymentScreenshot,
                'pending',
                $data['notes'] ?? ''
            ]);
            
            $orderId = $pdo->lastInsertId();
            // Fetch order details once (used for both admin + customer emails)
            $stmt = $pdo->prepare("SELECT o.*, p.title as product_title, p.price as product_price 
                                   FROM orders o 
                                   LEFT JOIN products p ON o.product_id = p.id 
                                   WHERE o.id = ?");
            $stmt->execute([$orderId]);
            $orderDetails = $stmt->fetch(PDO::FETCH_ASSOC);

            // IMPORTANT: These emails are triggered in this same request, before returning success to the frontend.
            // The frontend only shows the confirmation page after this request completes.
            $adminEmailResult = null;
            $customerEmailResult = null;

            if ($orderDetails && function_exists('sendNewOrderNotificationToAdmin')) {
                try {
                    $adminEmailResult = sendNewOrderNotificationToAdmin($orderDetails);
                } catch (Throwable $e) {
                    $adminEmailResult = ['success' => false, 'error' => $e->getMessage()];
                    error_log("Exception sending admin notification for order #" . $orderId . ": " . $e->getMessage());
                }
            }

            if ($orderDetails && !empty($orderDetails['customer_email']) && function_exists('sendPurchaseConfirmationEmail')) {
                try {
                    $customerEmailResult = sendPurchaseConfirmationEmail($orderDetails);
                } catch (Throwable $e) {
                    $customerEmailResult = ['success' => false, 'error' => $e->getMessage()];
                    error_log("Exception sending customer purchase confirmation for order #" . $orderId . ": " . $e->getMessage());
                }
            }
            
            ob_end_clean();
            sendSuccess([
                'id' => $orderId,
                'admin_email_sent' => (bool)($adminEmailResult['success'] ?? false),
                'customer_email_sent' => (bool)($customerEmailResult['success'] ?? false),
                'admin_email_error' => $adminEmailResult && empty($adminEmailResult['success']) ? ($adminEmailResult['error'] ?? 'Unknown error') : null
            ], 'Order created successfully');
        } catch (PDOException $e) {
            ob_end_clean();
            sendError('Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            ob_end_clean();
            sendError('Error creating order: ' . $e->getMessage());
        }
        break;
        
    case 'PUT':
        ob_clean();
        requireAdminLogin();
        
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            ob_end_clean();
            sendError('Invalid JSON data: ' . json_last_error_msg());
            exit;
        }
        
        if (!isset($data['id'])) {
            ob_end_clean();
            sendError('Order ID is required');
            exit;
        }
        
        $orderId = $data['id'];
        
        // Get current order data to check status change
        $stmt = $pdo->prepare("SELECT o.*, p.product_link as product_download_link 
                               FROM orders o 
                               LEFT JOIN products p ON o.product_id = p.id 
                               WHERE o.id = ?");
        $stmt->execute([$orderId]);
        $currentOrder = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$currentOrder) {
            ob_end_clean();
            sendError('Order not found');
            exit;
        }
        
        $wasPending = ($currentOrder['status'] === 'pending');
        $isApproving = (isset($data['status']) && $data['status'] === 'approved');
        $wasApproved = ($currentOrder['status'] === 'approved');

        // Base URL for normalizing relative links (and for emails)
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $baseUrl = $host ? ($protocol . '://' . $host) : '';

        // For "count only after approve": set approved_at when status transitions to approved
        $hasApprovedAt = false;
        if ($isApproving && !$wasApproved) {
            $hasApprovedAt = ensureOrdersApprovedAtColumnExists($pdo);
        }
        
        // If approving, automatically get product_link from product if not already set
        if ($isApproving && empty($currentOrder['product_link']) && !empty($currentOrder['product_download_link']) && !isLikelyImageUrl($currentOrder['product_download_link'])) {
            $data['product_link'] = $currentOrder['product_download_link'];
        }
        
        $allowedFields = ['status', 'notes', 'product_link'];
        $updates = [];
        $params = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        // Auto-set approved_at timestamp when approving (only if column exists)
        if ($isApproving && !$wasApproved && $hasApprovedAt) {
            $updates[] = "approved_at = CURRENT_TIMESTAMP";
        }
        
        if (empty($updates)) {
            ob_end_clean();
            sendError('No valid fields to update');
            exit;
        }
        
        $params[] = $orderId;
        $sql = "UPDATE orders SET " . implode(', ', $updates) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        
        $customerEmailSent = false;
        $customerEmailError = null;

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            // If order was just approved and has email/phone, automatically send product link
            if ($wasPending && $isApproving) {
                // Get updated order with product details
                $stmt = $pdo->prepare("SELECT o.*, p.title as product_title, p.product_link as product_download_link 
                                       FROM orders o 
                                       LEFT JOIN products p ON o.product_id = p.id 
                                       WHERE o.id = ?");
                $stmt->execute([$orderId]);
                $updatedOrder = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Pick the best download link (ignore image urls like /uploads/*.jpg and make relative links absolute)
                $productLink = normalizeDownloadLink(
                    $updatedOrder['product_link'] ?? '',
                    $updatedOrder['product_download_link'] ?? '',
                    $baseUrl
                );
                
                if (!empty($productLink) && !empty($updatedOrder['customer_email'])) {
                    // Automatically send email in background (don't wait for response)
                    try {
                        $emailResult = sendOrderApprovalEmail($updatedOrder, $productLink);
                        // Log the result for debugging
                        if (!$emailResult || !$emailResult['success']) {
                            error_log("Product link email FAILED for order #" . $orderId . ": " . json_encode($emailResult));
                            $customerEmailSent = false;
                            $customerEmailError = $emailResult['error'] ?? 'Unknown email error';
                        } else {
                            error_log("Product link email SENT successfully for order #" . $orderId . " to: " . ($emailResult['to'] ?? $updatedOrder['customer_email']));
                            $customerEmailSent = true;
                            $customerEmailError = null;
                        }
                    } catch (Exception $e) {
                        error_log("Exception sending product link email for order #" . $orderId . ": " . $e->getMessage());
                        $customerEmailSent = false;
                        $customerEmailError = $e->getMessage();
                    }
                } else {
                    if (empty($productLink)) {
                        error_log("Cannot send product link email for order #" . $orderId . ": Product link is empty. Please add product link in product settings or order details.");
                        $customerEmailSent = false;
                        $customerEmailError = 'Product link is empty. Please add product link in product settings or order details.';
                    }
                    if (empty($updatedOrder['customer_email'])) {
                        error_log("Cannot send product link email for order #" . $orderId . ": Customer email is missing.");
                        $customerEmailSent = false;
                        $customerEmailError = 'Customer email is missing.';
                    }
                }
            }
            
            ob_end_clean();
            sendSuccess([
                'customer_email_sent' => $customerEmailSent,
                'customer_email_error' => $customerEmailError
            ], 'Order updated successfully');
        } catch (PDOException $e) {
            ob_end_clean();
            sendError('Database error: ' . $e->getMessage());
        }
        break;
        
    case 'DELETE':
        ob_clean();
        requireAdminLogin();
        
        if (!isset($_GET['id'])) {
            ob_end_clean();
            sendError('Order ID is required');
            exit;
        }
        
        try {
            $orderId = intval($_GET['id']);
            if ($orderId <= 0) {
                ob_end_clean();
                sendError('Invalid Order ID');
                exit;
            }

            // Fetch order first so we can delete any associated uploaded files (payment proof, etc.)
            $stmt = $pdo->prepare("SELECT payment_screenshot, product_link FROM orders WHERE id = ?");
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                ob_end_clean();
                sendError('Order not found', 404);
                exit;
            }

            $filesToDelete = [];
            $paymentPath = resolveLocalUploadPath($order['payment_screenshot'] ?? '');
            if ($paymentPath) $filesToDelete[] = $paymentPath;

            // If product_link happens to be a local uploaded file, delete it too (safe-guarded).
            $productLinkPath = resolveLocalUploadPath($order['product_link'] ?? '');
            if ($productLinkPath) $filesToDelete[] = $productLinkPath;

            // Best-effort delete files
            $deletedFiles = [];
            $failedFiles = [];
            foreach (array_unique($filesToDelete) as $filePath) {
                if (!is_string($filePath) || $filePath === '') continue;
                if (file_exists($filePath) && is_file($filePath)) {
                    if (@unlink($filePath)) {
                        $deletedFiles[] = $filePath;
                    } else {
                        $failedFiles[] = $filePath;
                        error_log("Failed to delete order file: " . $filePath . " (order #" . $orderId . ")");
                    }
                }
            }

            // Hard delete from database
            $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$orderId]);
            
            ob_end_clean();
            $resp = ['deleted_files' => array_map('basename', $deletedFiles)];
            if (!empty($failedFiles)) {
                $resp['failed_files'] = array_map('basename', $failedFiles);
            }
            sendSuccess($resp, 'Order deleted successfully');
        } catch (PDOException $e) {
            ob_end_clean();
            sendError('Database error: ' . $e->getMessage());
        } catch (Exception $e) {
            ob_end_clean();
            sendError('Error deleting order: ' . $e->getMessage());
        }
        break;
        
    default:
        sendError('Method not allowed', 405);
}

/**
 * Automatically send email when order is approved
 */
function sendOrderApprovalEmail($order, $productLink) {
    try {
        // Check if SMTP is configured
        $smtpSettings = getSMTPSettings();
        if (empty($smtpSettings['smtp_host']) || empty($smtpSettings['smtp_email']) || empty($smtpSettings['smtp_password'])) {
            $errorMsg = "SMTP not configured. Please configure SMTP settings in Admin Panel → Settings.";
            error_log($errorMsg . " Order #" . ($order['id'] ?? 'unknown'));
            return ['success' => false, 'error' => $errorMsg];
        }
        
        $orderId = $order['id'];
        $customerEmail = htmlspecialchars($order['customer_email']);
        $customerName = htmlspecialchars($order['customer_name'] ?? 'Customer');
        $productTitle = htmlspecialchars($order['product_title'] ?? 'Digital Product');
        $safeProductLink = htmlspecialchars($productLink);
        $totalAmount = number_format($order['total_amount'], 2);
        $orderDate = !empty($order['created_at']) ? date('F j, Y, g:i a', strtotime($order['created_at'])) : date('F j, Y, g:i a');
        $paymentMethod = 'QR Payment (Banking/Esewa/Khalti)';

        // Build improved email message
        $emailSubject = "🎉 Payment Verified - Your Product Download Link";
        
        $emailMessage = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px 20px; background: #f9fafb; }
        .order-box { background: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #6b7280; }
        .info-value { color: #1f2937; text-align: right; }
        .button { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: 600; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; background: #f9fafb; }
        .success-badge { background: #d1fae5; color: #065f46; padding: 10px 15px; border-radius: 8px; margin: 20px 0; text-align: center; font-weight: 600; }
        .receipt { background: #ffffff; border-radius: 10px; padding: 18px; margin: 16px 0; border: 1px solid #e5e7eb; }
        .receipt h3 { margin: 0 0 12px 0; color:#111827; }
        .items { width: 100%; border-collapse: collapse; }
        .items th, .items td { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .items th { color:#6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; }
        .items td:last-child, .items th:last-child { text-align: right; }
        .total { font-weight: 800; color:#16a34a; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Payment Verified!</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Order #' . $orderId . '</p>
        </div>
        <div class="content">
            <p>Dear ' . $customerName . ',</p>
            <p>Great news! Your payment has been verified and your order has been approved.</p>
            
            <div class="success-badge">
                ✅ Your order is ready for download!
            </div>
            
            <div class="order-box">
                <h2 style="margin-top: 0; color: #1f2937;">Order Details</h2>
                <div class="info-row">
                    <span class="info-label">Order ID:</span>
                    <span class="info-value"><strong>#' . $orderId . '</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Date:</span>
                    <span class="info-value">' . htmlspecialchars($orderDate) . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Products:</span>
                    <span class="info-value"><strong>' . $productTitle . '</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Amount Paid:</span>
                    <span class="info-value" style="color: #22c55e; font-weight: 700;">रु' . $totalAmount . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-value">' . htmlspecialchars($paymentMethod) . '</span>
                </div>
            </div>

            <div class="receipt">
              <h3>Receipt / Invoice</h3>
              <table class="items">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>' . $productTitle . '</td>
                    <td>रु' . $totalAmount . '</td>
                  </tr>
                  <tr>
                    <td class="total">Total</td>
                    <td class="total">रु' . $totalAmount . '</td>
                  </tr>
                </tbody>
              </table>
              <p style="margin:12px 0 0 0; color:#6b7280; font-size: 12px;">
                Keep this email as your proof of purchase.
              </p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 15px;">Download Your Product:</p>
                <a href="' . $safeProductLink . '" class="button" target="_blank">📥 Download Product Now</a>
            </div>
            
            <div style="background: #f0fdf4; border-left: 4px solid #22c55e; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <strong>📋 Download Instructions:</strong>
                <ol style="margin: 10px 0; padding-left: 20px;">
                    <li>Click the "Download Product Now" button above</li>
                    <li>Or copy and paste this link in your browser:</li>
                </ol>
                <p style="word-break: break-all; background: #fff; padding: 10px; border-radius: 5px; margin: 10px 0; font-family: monospace; font-size: 0.9rem;">' . $safeProductLink . '</p>
            </div>
            
            <p>If you have any questions or need assistance, please don\'t hesitate to contact us.</p>
            <p>Thank you for your purchase!</p>
            <p>Best regards,<br><strong>Hamro Digi Cart Team</strong></p>
        </div>
        <div class="footer">
            <p>This is an automated email. Please do not reply.</p>
            <p>Order Management System</p>
        </div>
    </div>
</body>
</html>';
        
        error_log("Sending product download link email to customer: " . $customerEmail . " for order #" . $orderId);
        
        $result = sendSMTPEmail(
            $customerEmail,
            $emailSubject,
            $emailMessage,
            $smtpSettings['smtp_email'] ?? null,
            $smtpSettings['smtp_from_name'] ?? null
        );
        
        if (!$result['success']) {
            $errorMsg = "Failed to send product link email for order #" . $orderId . ": " . ($result['error'] ?? 'Unknown error');
            error_log($errorMsg);
            return ['success' => false, 'error' => $result['error'] ?? 'Unknown error'];
        }
        
        error_log("Product link email sent successfully to " . $customerEmail . " for order #" . $orderId);
        return ['success' => true, 'message' => 'Email sent successfully', 'to' => $customerEmail];
    } catch (Exception $e) {
        $errorMsg = "Exception sending product link email for order #" . ($order['id'] ?? 'unknown') . ": " . $e->getMessage();
        error_log($errorMsg);
        error_log("Exception trace: " . $e->getTraceAsString());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
