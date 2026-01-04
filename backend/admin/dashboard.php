<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$pdo = getDBConnection();

// Get statistics
$stats = [
    'products' => $pdo->query("SELECT COUNT(*) as count FROM products")->fetch()['count'],
    'categories' => $pdo->query("SELECT COUNT(*) as count FROM categories")->fetch()['count'],
    'orders' => $pdo->query("SELECT COUNT(*) as count FROM orders")->fetch()['count'],
    'pending_orders' => $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")->fetch()['count']
];

// Get purchase amounts for different time periods (only orders approved from now on)
// NOTE: We use approved_at so totals only start counting after admin clicks Approve.
$todayStart = date('Y-m-d 00:00:00');
$todayEnd = date('Y-m-d 23:59:59');

// Ensure approved_at column exists (self-heal). If it can't be added, show 0 totals (no fatal error).
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

    // All-time purchase amount (orders approved from now on)
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) as total 
                           FROM orders 
                           WHERE status IN ('approved','completed') 
                             AND approved_at IS NOT NULL");
    $stmt->execute();
    $allTimePurchase = floatval($stmt->fetch()['total'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hamro Digi Cart</title>
    <?php include 'includes/favicon.php'; ?>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-main">
            <?php include 'includes/header.php'; ?>
            
            <div class="admin-content">
                <h1>Dashboard</h1>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">📦</div>
                        <div class="stat-info">
                            <h3><?php echo $stats['products']; ?></h3>
                            <p>Total Products</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">📁</div>
                        <div class="stat-info">
                            <h3><?php echo $stats['categories']; ?></h3>
                            <p>Categories</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">🛒</div>
                        <div class="stat-info">
                            <h3><?php echo $stats['orders']; ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">⏳</div>
                        <div class="stat-info">
                            <h3><?php echo $stats['pending_orders']; ?></h3>
                            <p>Pending Orders</p>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Purchase Amounts Section -->
                <div class="purchase-amounts-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2>Purchase Amounts Overview</h2>
                        <small style="color: #95a5a6; font-size: 12px;" id="refresh-indicator">🔄 Auto-updates every 10 seconds</small>
                    </div>
                    <?php if (!$hasApprovedAt): ?>
                        <div style="background:#fff3cd; border-left:4px solid #f59e0b; padding:12px 14px; border-radius:10px; margin-bottom: 14px; color:#856404;">
                            <strong>Note:</strong> Sales totals will start updating after you approve new orders. (Database is missing <code>approved_at</code>. Run <code>backend/database/alter_orders_add_approved_at.sql</code> if needed.)
                        </div>
                    <?php endif; ?>
                    <div class="stats-grid">
                        <div class="stat-card purchase-card">
                            <div class="stat-icon">📅</div>
                            <div class="stat-info">
                                <h3 id="today-amount">रु<?php echo number_format($todayPurchase, 2); ?></h3>
                                <p>Today's Purchase</p>
                                <small>(Last 1 Day)</small>
                            </div>
                        </div>
                        
                        <div class="stat-card purchase-card">
                            <div class="stat-icon">📊</div>
                            <div class="stat-info">
                                <h3 id="seven-days-amount">रु<?php echo number_format($sevenDaysPurchase, 2); ?></h3>
                                <p>Last 7 Days</p>
                                <small>(Weekly Total)</small>
                            </div>
                        </div>
                        
                        <div class="stat-card purchase-card">
                            <div class="stat-icon">📈</div>
                            <div class="stat-info">
                                <h3 id="one-month-amount">रु<?php echo number_format($oneMonthPurchase, 2); ?></h3>
                                <p>Last 30 Days</p>
                                <small>(Monthly Total)</small>
                            </div>
                        </div>
                        
                        <div class="stat-card purchase-card">
                            <div class="stat-icon">💰</div>
                            <div class="stat-info">
                                <h3 id="all-time-amount">रु<?php echo number_format($allTimePurchase, 2); ?></h3>
                                <p>All Time Total</p>
                                <small>(Lifetime Sales)</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="recent-orders">
                    <h2>Recent Orders</h2>
                    <div id="recent-orders-list">Loading...</div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/admin.js"></script>
    <script>
        // Function to format currency
        function formatCurrency(amount) {
            return 'रु' + parseFloat(amount).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
        
        // Function to update purchase amounts via AJAX
        function updatePurchaseAmounts() {
            fetch('../api/dashboard-stats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        // Update all purchase amounts
                        document.getElementById('today-amount').textContent = formatCurrency(data.data.today);
                        document.getElementById('seven-days-amount').textContent = formatCurrency(data.data.sevenDays);
                        document.getElementById('one-month-amount').textContent = formatCurrency(data.data.oneMonth);
                        document.getElementById('all-time-amount').textContent = formatCurrency(data.data.allTime);
                        
                        // Update refresh indicator
                        const now = new Date();
                        document.getElementById('refresh-indicator').textContent = 
                            '🔄 Last updated: ' + now.toLocaleTimeString();
                    }
                })
                .catch(error => {
                    console.error('Error updating purchase amounts:', error);
                });
        }
        
        // Auto-refresh purchase amounts every 10 seconds (more responsive)
        setInterval(updatePurchaseAmounts, 10000);
        
        // Load recent orders
        fetch('../api/orders.php?status=pending')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    const html = data.data.slice(0, 5).map(order => `
                        <div class="order-item">
                            <strong>Order #${order.id}</strong> - ${order.customer_email} - रु${order.total_amount}
                            <span class="status-badge status-${order.status}">${order.status}</span>
                        </div>
                    `).join('');
                    document.getElementById('recent-orders-list').innerHTML = html;
                } else {
                    document.getElementById('recent-orders-list').innerHTML = '<p>No pending orders</p>';
                }
            });
    </script>
</body>
</html>






