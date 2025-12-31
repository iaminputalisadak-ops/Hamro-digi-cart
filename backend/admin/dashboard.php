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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hamro Digi Cart</title>
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
                
                <div class="recent-orders">
                    <h2>Recent Orders</h2>
                    <div id="recent-orders-list">Loading...</div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/admin.js"></script>
    <script>
        // Load recent orders
        fetch('../api/orders.php?status=pending')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    const html = data.data.slice(0, 5).map(order => `
                        <div class="order-item">
                            <strong>Order #${order.id}</strong> - ${order.customer_email} - ₹${order.total_amount}
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





