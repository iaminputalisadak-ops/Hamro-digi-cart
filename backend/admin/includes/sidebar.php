<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h2>Hamro Digi Cart</h2>
        <p>Admin Panel</p>
    </div>
    
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
            <span>📊</span> Dashboard
        </a>
        <a href="products.php" class="<?php echo $currentPage === 'products.php' ? 'active' : ''; ?>">
            <span>📦</span> Products
        </a>
        <a href="categories.php" class="<?php echo $currentPage === 'categories.php' ? 'active' : ''; ?>">
            <span>📁</span> Categories
        </a>
        <a href="orders.php" class="<?php echo $currentPage === 'orders.php' ? 'active' : ''; ?>">
            <span>🛒</span> Orders
        </a>
        <a href="offers.php" class="<?php echo $currentPage === 'offers.php' ? 'active' : ''; ?>">
            <span>🎁</span> Offers
        </a>
        <a href="pages.php" class="<?php echo $currentPage === 'pages.php' ? 'active' : ''; ?>">
            <span>📄</span> Pages
        </a>
        <a href="settings.php" class="<?php echo $currentPage === 'settings.php' ? 'active' : ''; ?>">
            <span>⚙️</span> Settings
        </a>
        <a href="website-settings.php" class="<?php echo $currentPage === 'website-settings.php' ? 'active' : ''; ?>">
            <span>🌐</span> Website Settings
        </a>
        <a href="product-card-settings.php" class="<?php echo $currentPage === 'product-card-settings.php' ? 'active' : ''; ?>">
            <span>🃏</span> Product Card Settings
        </a>
        <a href="send-email.php" class="<?php echo $currentPage === 'send-email.php' ? 'active' : ''; ?>">
            <span>📧</span> Send Email
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn">🚪 Logout</a>
    </div>
</aside>

