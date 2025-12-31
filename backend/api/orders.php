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

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

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
                // If order doesn't have product_link but product does, use product's link
                if (empty($order['product_link']) && !empty($order['product_download_link'])) {
                    $order['product_link'] = $order['product_download_link'];
                }
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
                    $paymentScreenshot = $baseUrl . '/uploads/' . $filename;
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
            ob_end_clean();
            sendSuccess(['id' => $orderId], 'Order created successfully');
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
        
        // If approving, automatically get product_link from product if not already set
        if ($isApproving && empty($currentOrder['product_link']) && !empty($currentOrder['product_download_link'])) {
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
        
        if (empty($updates)) {
            ob_end_clean();
            sendError('No valid fields to update');
            exit;
        }
        
        $params[] = $orderId;
        $sql = "UPDATE orders SET " . implode(', ', $updates) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        
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
                
                // Use product_link from order (may have been auto-set from product)
                $productLink = !empty($updatedOrder['product_link']) ? $updatedOrder['product_link'] : $updatedOrder['product_download_link'];
                
                if (!empty($productLink) && !empty($updatedOrder['customer_email'])) {
                    // Automatically send email in background (don't wait for response)
                    sendOrderApprovalEmail($updatedOrder, $productLink);
                }
            }
            
            ob_end_clean();
            sendSuccess([], 'Order updated successfully');
        } catch (PDOException $e) {
            ob_end_clean();
            sendError('Database error: ' . $e->getMessage());
        }
        break;
        
    case 'DELETE':
        requireAdminLogin();
        
        if (!isset($_GET['id'])) {
            sendError('Order ID is required');
        }
        
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        
        sendSuccess([], 'Order deleted successfully');
        break;
        
    default:
        sendError('Method not allowed', 405);
}

/**
 * Automatically send email when order is approved
 */
function sendOrderApprovalEmail($order, $productLink) {
    try {
        $productTitle = htmlspecialchars($order['product_title'] ?? 'Digital Product');
        $safeProductLink = htmlspecialchars($productLink);
        
        $emailMessage = '<html><head><style>body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; } .container { max-width: 600px; margin: 0 auto; padding: 20px; } .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; } .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; } .button { display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; } .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }</style></head><body><div class="container"><div class="header"><h1>🎉 Payment Verified!</h1></div><div class="content"><p>Dear Customer,</p><p>Thank you for your purchase! Your payment has been verified.</p><p><strong>Order ID:</strong> #' . $order['id'] . '</p><p><strong>Product:</strong> ' . $productTitle . '</p><p>Click the button below to download your product:</p><div style="text-align: center;"><a href="' . $safeProductLink . '" class="button" target="_blank">Download Product</a></div><p>Or copy this link:</p><p style="word-break: break-all; background: #fff; padding: 10px; border-radius: 5px;">' . $safeProductLink . '</p><p>If you have any questions, please contact us.</p><p>Best regards,<br>Hamro Digi Cart Team</p></div><div class="footer"><p>This is an automated email. Please do not reply.</p></div></div></body></html>';
        
        $result = sendSMTPEmail(
            $order['customer_email'],
            'Your Product Download Link - Hamro Digi Cart',
            $emailMessage
        );
        
        // Log success/failure but don't throw exceptions (background process)
        if (!$result['success']) {
            error_log("Failed to send approval email for order #" . $order['id'] . ": " . ($result['error'] ?? 'Unknown error'));
        }
    } catch (Exception $e) {
        // Log error but don't fail the order update
        error_log("Exception sending approval email for order #" . $order['id'] . ": " . $e->getMessage());
    }
}
