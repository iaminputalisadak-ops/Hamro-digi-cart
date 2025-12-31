<?php
/**
 * Categories API
 */
require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $category = $stmt->fetch();
            
            if ($category) {
                sendSuccess($category);
            } else {
                sendError('Category not found', 404);
            }
        } else {
            $slug = isset($_GET['slug']) ? $_GET['slug'] : null;
            
            if ($slug) {
                $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
                $stmt->execute([$slug]);
                $category = $stmt->fetch();
                sendSuccess($category ? [$category] : []);
            } else {
                $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
                $categories = $stmt->fetchAll();
                sendSuccess($categories);
            }
        }
        break;
        
    case 'POST':
        requireAdminLogin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || !isset($data['slug'])) {
            sendError('Name and slug are required');
        }
        
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'] ?? ''
        ]);
        
        $categoryId = $pdo->lastInsertId();
        sendSuccess(['id' => $categoryId], 'Category created successfully');
        break;
        
    case 'PUT':
        requireAdminLogin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id'])) {
            sendError('Category ID is required');
        }
        
        $stmt = $pdo->prepare("UPDATE categories SET 
                               name = ?, slug = ?, description = ?, updated_at = CURRENT_TIMESTAMP
                               WHERE id = ?");
        $stmt->execute([
            $data['name'] ?? '',
            $data['slug'] ?? '',
            $data['description'] ?? '',
            $data['id']
        ]);
        
        sendSuccess([], 'Category updated successfully');
        break;
        
    case 'DELETE':
        requireAdminLogin();
        
        if (!isset($_GET['id'])) {
            sendError('Category ID is required');
        }
        
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        
        sendSuccess([], 'Category deleted successfully');
        break;
        
    default:
        sendError('Method not allowed', 405);
}





