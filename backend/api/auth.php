<?php
/**
 * Authentication API
 */
require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'POST':
        $action = $_GET['action'] ?? '';
        
        if ($action === 'login') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['username']) || !isset($data['password'])) {
                sendError('Username and password are required');
            }
            
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$data['username']]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($data['password'], $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                sendSuccess([
                    'id' => $admin['id'],
                    'username' => $admin['username'],
                    'email' => $admin['email']
                ], 'Login successful');
            } else {
                sendError('Invalid credentials', 401);
            }
        } elseif ($action === 'logout') {
            session_destroy();
            sendSuccess([], 'Logout successful');
        } elseif ($action === 'check') {
            if (isAdminLoggedIn()) {
                sendSuccess([
                    'logged_in' => true,
                    'username' => $_SESSION['admin_username']
                ]);
            } else {
                sendSuccess(['logged_in' => false]);
            }
        } else {
            sendError('Invalid action', 400);
        }
        break;
        
    default:
        sendError('Method not allowed', 405);
}






