<div class="admin-header">
    <div class="header-left">
        <h1><?php echo ucfirst(str_replace('.php', '', basename($_SERVER['PHP_SELF']))); ?></h1>
    </div>
    <div class="header-right">
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
    </div>
</div>





