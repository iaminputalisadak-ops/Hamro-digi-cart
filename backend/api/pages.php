<?php
/**
 * Pages API
 */
require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $page = $stmt->fetch();
            
            if ($page) {
                sendSuccess($page);
            } else {
                sendError('Page not found', 404);
            }
        } elseif (isset($_GET['key'])) {
            $stmt = $pdo->prepare("SELECT * FROM pages WHERE page_key = ?");
            $stmt->execute([$_GET['key']]);
            $page = $stmt->fetch();
            
            if ($page) {
                sendSuccess($page);
            } else {
                sendError('Page not found', 404);
            }
        } else {
            $stmt = $pdo->query("SELECT * FROM pages ORDER BY title ASC");
            $pages = $stmt->fetchAll();
            sendSuccess($pages);
        }
        break;
        
    case 'POST':
        requireAdminLogin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['page_key']) || !isset($data['title']) || !isset($data['route'])) {
            sendError('Page key, title, and route are required');
        }
        
        $stmt = $pdo->prepare("INSERT INTO pages (page_key, title, content, route) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['page_key'],
            $data['title'],
            $data['content'] ?? '',
            $data['route']
        ]);
        
        $pageId = $pdo->lastInsertId();
        sendSuccess(['id' => $pageId], 'Page created successfully');
        break;
        
    case 'PUT':
        requireAdminLogin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id'])) {
            sendError('Page ID is required');
        }
        
        $stmt = $pdo->prepare("UPDATE pages SET 
                               title = ?, content = ?, route = ?, updated_at = CURRENT_TIMESTAMP
                               WHERE id = ?");
        $stmt->execute([
            $data['title'] ?? '',
            $data['content'] ?? '',
            $data['route'] ?? '',
            $data['id']
        ]);
        
        sendSuccess([], 'Page updated successfully');
        break;
        
    case 'DELETE':
        requireAdminLogin();
        
        if (!isset($_GET['id'])) {
            sendError('Page ID is required');
        }
        
        $stmt = $pdo->prepare("DELETE FROM pages WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        
        sendSuccess([], 'Page deleted successfully');
        break;
        
    default:
        sendError('Method not allowed', 405);
}

