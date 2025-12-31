<?php
/**
 * Offers API
 */
require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM offers WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $offer = $stmt->fetch();
            
            if ($offer) {
                sendSuccess($offer);
            } else {
                sendError('Offer not found', 404);
            }
        } else {
            $status = isset($_GET['status']) ? $_GET['status'] : null;
            
            $sql = "SELECT * FROM offers";
            $params = [];
            
            if ($status) {
                $sql .= " WHERE status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $offers = $stmt->fetchAll();
            
            sendSuccess($offers);
        }
        break;
        
    case 'POST':
        requireAdminLogin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['title']) || !isset($data['discount_value'])) {
            sendError('Title and discount value are required');
        }
        
        $discountType = isset($data['discount_type']) ? $data['discount_type'] : 'percentage';
        $status = isset($data['status']) ? $data['status'] : 'active';
        
        $stmt = $pdo->prepare("INSERT INTO offers (title, description, discount_type, discount_value, start_date, end_date, image, link, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['title'],
            $data['description'] ?? '',
            $discountType,
            $data['discount_value'],
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['image'] ?? '',
            $data['link'] ?? '',
            $status
        ]);
        
        $offerId = $pdo->lastInsertId();
        sendSuccess(['id' => $offerId], 'Offer created successfully');
        break;
        
    case 'PUT':
        requireAdminLogin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id'])) {
            sendError('Offer ID is required');
        }
        
        $stmt = $pdo->prepare("UPDATE offers SET 
                               title = ?, description = ?, discount_type = ?, discount_value = ?,
                               start_date = ?, end_date = ?, image = ?, link = ?, status = ?,
                               updated_at = CURRENT_TIMESTAMP
                               WHERE id = ?");
        $stmt->execute([
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['discount_type'] ?? 'percentage',
            $data['discount_value'] ?? 0,
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            $data['image'] ?? '',
            $data['link'] ?? '',
            $data['status'] ?? 'active',
            $data['id']
        ]);
        
        sendSuccess([], 'Offer updated successfully');
        break;
        
    case 'DELETE':
        requireAdminLogin();
        
        if (!isset($_GET['id'])) {
            sendError('Offer ID is required');
        }
        
        $stmt = $pdo->prepare("DELETE FROM offers WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        
        sendSuccess([], 'Offer deleted successfully');
        break;
        
    default:
        sendError('Method not allowed', 405);
}




