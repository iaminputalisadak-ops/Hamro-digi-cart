<?php
/**
 * Settings API
 */
require_once __DIR__ . '/../config/config.php';

// Suppress error display for clean JSON output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

ob_start();

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'GET':
        ob_clean();
        
        // Get all settings or specific setting
        if (isset($_GET['key'])) {
            $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
            $stmt->execute([$_GET['key']]);
            $setting = $stmt->fetch();
            
            if ($setting) {
                sendSuccess(['value' => $setting['setting_value']]);
            } else {
                sendSuccess(['value' => null]);
            }
        } else {
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
            $settings = $stmt->fetchAll();
            
            $settingsArray = [];
            foreach ($settings as $setting) {
                $settingsArray[$setting['setting_key']] = $setting['setting_value'];
            }
            
            sendSuccess($settingsArray);
        }
        break;
        
    case 'POST':
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
        
        if (!isset($data['key']) || !isset($data['value'])) {
            ob_end_clean();
            sendError('Setting key and value are required');
            exit;
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) 
                                   VALUES (?, ?) 
                                   ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = CURRENT_TIMESTAMP");
            $stmt->execute([$data['key'], $data['value'], $data['value']]);
            
            ob_end_clean();
            sendSuccess([], 'Setting saved successfully');
        } catch (PDOException $e) {
            ob_end_clean();
            sendError('Database error: ' . $e->getMessage());
        }
        break;
        
    default:
        ob_end_clean();
        sendError('Method not allowed', 405);
}





