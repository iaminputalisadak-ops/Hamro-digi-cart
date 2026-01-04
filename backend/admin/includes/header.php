<div class="admin-header">
    <div class="header-left">
        <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <h1><?php echo ucfirst(str_replace('.php', '', basename($_SERVER['PHP_SELF']))); ?></h1>
    </div>
    <div class="header-right">
        <span class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
    </div>
</div>





