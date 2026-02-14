<?php
/**
 * Products API
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/sitemap.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'GET':
        // Get search suggestions (autocomplete)
        if (isset($_GET['suggest']) && !empty($_GET['suggest'])) {
            $query = trim($_GET['suggest']);
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            
            if (strlen($query) < 2) {
                sendSuccess([]);
                break;
            }
            
            // Get product titles that match the query (limit results for autocomplete)
            $searchTerm = "%$query%";
            $exactMatch = "$query%";
            
            // Use a simpler query with proper ordering
            $stmt = $pdo->prepare("SELECT DISTINCT p.id, p.title, c.name as category_name 
                                   FROM products p 
                                   LEFT JOIN categories c ON p.category_id = c.id 
                                   WHERE p.status = 'active' 
                                   AND (p.title LIKE ? OR p.description LIKE ?)
                                   ORDER BY p.title ASC
                                   LIMIT ?");
            $stmt->execute([$searchTerm, $searchTerm, $limit]);
            $suggestions = $stmt->fetchAll();
            
            // Sort suggestions: exact matches first, then partial matches
            usort($suggestions, function($a, $b) use ($query) {
                $aStarts = stripos($a['title'], $query) === 0;
                $bStarts = stripos($b['title'], $query) === 0;
                if ($aStarts && !$bStarts) return -1;
                if (!$aStarts && $bStarts) return 1;
                return strcasecmp($a['title'], $b['title']);
            });
            
            // Format suggestions for autocomplete
            $formatted = array_map(function($item) {
                return [
                    'id' => $item['id'],
                    'title' => $item['title'],
                    'category' => $item['category_name'] ?? ''
                ];
            }, $suggestions);
            
            sendSuccess($formatted);
            break;
        }
        
        // Get all products or single product
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                                   FROM products p 
                                   LEFT JOIN categories c ON p.category_id = c.id 
                                   WHERE p.id = ?");
            $stmt->execute([$_GET['id']]);
            $product = $stmt->fetch();
            
            if ($product) {
                sendSuccess($product);
            } else {
                sendError('Product not found', 404);
            }
        } else {
            $categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : null;
            $status = isset($_GET['status']) ? $_GET['status'] : 'active';
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            
            $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.status = ?";
            $params = [$status];
            
            if ($categoryId) {
                $sql .= " AND p.category_id = ?";
                $params[] = $categoryId;
            }
            
            if ($search) {
                $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $sql .= " ORDER BY p.created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll();
            
            sendSuccess($products);
        }
        break;
        
    case 'POST':
        requireAdminLogin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['title']) || !isset($data['price'])) {
            sendError('Title and price are required');
        }
        
        $status = isset($data['status']) ? $data['status'] : 'active';
        
        $stmt = $pdo->prepare("INSERT INTO products (title, description, price, discount, category_id, image, product_link, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['title'],
            $data['description'] ?? '',
            $data['price'],
            $data['discount'] ?? 0,
            $data['category_id'] ?? null,
            $data['image'] ?? '',
            $data['product_link'] ?? '',
            $status
        ]);
        
        $productId = $pdo->lastInsertId();
        
        // Fetch the complete product data to return
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                               FROM products p 
                               LEFT JOIN categories c ON p.category_id = c.id 
                               WHERE p.id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        // Auto-update sitemap after product changes (best-effort)
        try {
            writePublicSitemap($pdo);
        } catch (Throwable $e) {
            // ignore
        }

        sendSuccess($product, 'Product created successfully');
        break;
        
    case 'PUT':
        requireAdminLogin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id'])) {
            sendError('Product ID is required');
        }
        
        $stmt = $pdo->prepare("UPDATE products SET 
                               title = ?, description = ?, price = ?, discount = ?, 
                               category_id = ?, image = ?, product_link = ?, status = ?, updated_at = CURRENT_TIMESTAMP
                               WHERE id = ?");
        $stmt->execute([
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['price'] ?? 0,
            $data['discount'] ?? 0,
            $data['category_id'] ?? null,
            $data['image'] ?? '',
            $data['product_link'] ?? '',
            $data['status'] ?? 'active',
            $data['id']
        ]);

        // Auto-update sitemap after product changes (best-effort)
        try {
            writePublicSitemap($pdo);
        } catch (Throwable $e) {
            // ignore
        }

        sendSuccess([], 'Product updated successfully');
        break;
        
    case 'DELETE':
        requireAdminLogin();
        
        if (!isset($_GET['id'])) {
            sendError('Product ID is required');
        }
        
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$_GET['id']]);

        // Auto-update sitemap after product changes (best-effort)
        try {
            writePublicSitemap($pdo);
        } catch (Throwable $e) {
            // ignore
        }

        sendSuccess([], 'Product deleted successfully');
        break;
        
    default:
        sendError('Method not allowed', 405);
}

