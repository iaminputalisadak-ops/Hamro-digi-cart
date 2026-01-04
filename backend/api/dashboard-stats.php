<?php
/**
 * Dashboard Statistics API
 * Returns purchase amounts for different time periods
 */

require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

header('Content-Type: application/json');

$pdo = getDBConnection();

// Get purchase amounts for different time periods (only approved orders)
$todayStart = date('Y-m-d 00:00:00');
$todayEnd = date('Y-m-d 23:59:59');

// Ensure approved_at exists (self-heal). If it can't be added, return 0 totals (no fatal error).
$hasApprovedAt = false;
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'approved_at'");
    $hasApprovedAt = (bool)$stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $hasApprovedAt = false;
}

if (!$hasApprovedAt) {
    try {
        $pdo->exec("ALTER TABLE orders ADD COLUMN approved_at DATETIME NULL");
        $pdo->exec("ALTER TABLE orders ADD INDEX idx_approved_at (approved_at)");
        $hasApprovedAt = true;
    } catch (Throwable $e) {
        $hasApprovedAt = false;
    }
}

$todayPurchase = 0.0;
$sevenDaysPurchase = 0.0;
$oneMonthPurchase = 0.0;
$allTimePurchase = 0.0;

if ($hasApprovedAt) {
    // Today's purchase amount (based on approval time)
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) as total 
                           FROM orders 
                           WHERE status IN ('approved','completed') 
                             AND approved_at IS NOT NULL 
                             AND approved_at >= ? AND approved_at <= ?");
    $stmt->execute([$todayStart, $todayEnd]);
    $todayPurchase = floatval($stmt->fetch()['total'] ?? 0);

    // Last 7 days purchase amount
    $sevenDaysAgo = date('Y-m-d 00:00:00', strtotime('-7 days'));
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) as total 
                           FROM orders 
                           WHERE status IN ('approved','completed') 
                             AND approved_at IS NOT NULL 
                             AND approved_at >= ? AND approved_at <= ?");
    $stmt->execute([$sevenDaysAgo, $todayEnd]);
    $sevenDaysPurchase = floatval($stmt->fetch()['total'] ?? 0);

    // Last 30 days (1 month) purchase amount
    $oneMonthAgo = date('Y-m-d 00:00:00', strtotime('-30 days'));
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) as total 
                           FROM orders 
                           WHERE status IN ('approved','completed') 
                             AND approved_at IS NOT NULL 
                             AND approved_at >= ? AND approved_at <= ?");
    $stmt->execute([$oneMonthAgo, $todayEnd]);
    $oneMonthPurchase = floatval($stmt->fetch()['total'] ?? 0);

    // All-time purchase amount (all approved orders)
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) as total 
                           FROM orders 
                           WHERE status IN ('approved','completed') 
                             AND approved_at IS NOT NULL");
    $stmt->execute();
    $allTimePurchase = floatval($stmt->fetch()['total'] ?? 0);
}

echo json_encode([
    'success' => true,
    'data' => [
        'hasApprovedAt' => $hasApprovedAt,
        'today' => $todayPurchase,
        'sevenDays' => $sevenDaysPurchase,
        'oneMonth' => $oneMonthPurchase,
        'allTime' => $allTimePurchase
    ]
]);

