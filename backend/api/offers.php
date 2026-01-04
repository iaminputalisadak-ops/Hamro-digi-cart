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
        
        if (!isset($data['title']) || empty(trim($data['title']))) {
            sendError('Offer name is required');
        }
        
        $title = trim($data['title']);
        
        // Set default values for other fields
        // Use link field to store category name for filtering products
        $link = isset($data['link']) ? trim($data['link']) : '';
        $stmt = $pdo->prepare("INSERT INTO offers (title, description, discount_type, discount_value, start_date, end_date, image, link, status) 
                               VALUES (?, '', 'percentage', 0, NULL, NULL, '', ?, 'active')");
        $stmt->execute([$title, $link]);
        
        $offerId = $pdo->lastInsertId();
        sendSuccess(['id' => $offerId], 'Offer created successfully');
        break;
        
    case 'PUT':
        requireAdminLogin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id'])) {
            sendError('Offer ID is required');
        }
        
        if (!isset($data['title']) || empty(trim($data['title']))) {
            sendError('Offer name is required');
        }
        
        $title = trim($data['title']);
        $link = isset($data['link']) ? trim($data['link']) : '';
        
        // Update title and link (category filter) fields
        $stmt = $pdo->prepare("UPDATE offers SET 
                               title = ?,
                               link = ?,
                               updated_at = CURRENT_TIMESTAMP
                               WHERE id = ?");
        $stmt->execute([
            $title,
            $link,
            $data['id']
        ]);
        
        sendSuccess([], 'Offer updated successfully');
        break;
        
    case 'DELETE':
        requireAdminLogin();
        
        // Check if delete_all parameter is set
        if (isset($_GET['delete_all']) && $_GET['delete_all'] == '1') {
            // Bulk delete is intentionally disabled for safety.
            sendError('Delete All offers is disabled', 403);
        } else {
            // Delete single offer
            if (!isset($_GET['id'])) {
                sendError('Offer ID is required');
            }
            
            $stmt = $pdo->prepare("DELETE FROM offers WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            
            sendSuccess([], 'Offer deleted successfully');
        }
        break;
        
    default:
        sendError('Method not allowed', 405);
}




