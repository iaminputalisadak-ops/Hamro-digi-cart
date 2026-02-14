<?php
/**
 * Database Viewer
 * Simple interface to view and manage database
 * 
 * SECURITY: This page should be protected in production!
 */

require_once __DIR__ . '/config/config.php';
requireAdminLogin(); // Require admin login for security

$pdo = getDBConnection();
$message = '';
$error = '';

// Get database info
try {
    $dbInfo = [
        'host' => DB_HOST,
        'port' => DB_PORT,
        'database' => DB_NAME,
        'username' => DB_USER,
        'charset' => DB_CHARSET
    ];
    
    // Get table list
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    // Get selected table data
    $selectedTable = $_GET['table'] ?? '';
    $tableData = [];
    $tableColumns = [];
    
    if ($selectedTable && in_array($selectedTable, $tables)) {
        $tableColumns = $pdo->query("DESCRIBE `$selectedTable`")->fetchAll(PDO::FETCH_ASSOC);
        $tableData = $pdo->query("SELECT * FROM `$selectedTable` LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get database stats
    $stats = [];
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) as count FROM `$table`")->fetch(PDO::FETCH_ASSOC);
        $stats[$table] = $count['count'];
    }
    
} catch (Exception $e) {
    $error = 'Database Error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Viewer - Admin Panel</title>
    <?php include 'admin/includes/favicon.php'; ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        h1 {
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #6b7280;
            margin-bottom: 30px;
        }
        
        .info-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 30px;
        }
        
        .info-box h3 {
            color: #0369a1;
            margin-bottom: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-weight: 600;
            color: #1e293b;
        }
        
        .tables-section {
            margin-bottom: 30px;
        }
        
        .tables-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .table-card {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .table-card:hover {
            border-color: #22c55e;
            background: #f0fdf4;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
        }
        
        .table-card.active {
            border-color: #22c55e;
            background: #dcfce7;
        }
        
        .table-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .table-count {
            font-size: 0.85rem;
            color: #6b7280;
        }
        
        .table-viewer {
            margin-top: 30px;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .table-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .table-wrapper {
            overflow-x: auto;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        th {
            background: #f9fafb;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            position: sticky;
            top: 0;
        }
        
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
            color: #1f2937;
        }
        
        tr:hover {
            background: #f9fafb;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #22c55e;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background 0.2s;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #16a34a;
        }
        
        .btn-secondary {
            background: #6b7280;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #22c55e;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .tables-grid {
                grid-template-columns: 1fr;
            }
            
            .table-wrapper {
                font-size: 0.85rem;
            }
            
            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/admin/dashboard.php" class="back-link">← Back to Dashboard</a>
        
        <h1>🗄️ Database Viewer</h1>
        <p class="subtitle">View and manage your database tables</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <!-- Database Info -->
        <div class="info-box">
            <h3>📊 Database Connection Info</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Host</span>
                    <span class="info-value"><?php echo htmlspecialchars($dbInfo['host']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Port</span>
                    <span class="info-value"><?php echo htmlspecialchars($dbInfo['port']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Database</span>
                    <span class="info-value"><?php echo htmlspecialchars($dbInfo['database']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Username</span>
                    <span class="info-value"><?php echo htmlspecialchars($dbInfo['username']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Charset</span>
                    <span class="info-value"><?php echo htmlspecialchars($dbInfo['charset']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <span class="info-value"><span class="badge badge-success">Connected</span></span>
                </div>
            </div>
        </div>
        
        <!-- Tables List -->
        <div class="tables-section">
            <h2 style="margin-bottom: 15px;">📋 Database Tables (<?php echo count($tables); ?>)</h2>
            <div class="tables-grid">
                <?php foreach ($tables as $table): ?>
                    <a href="?table=<?php echo urlencode($table); ?>" 
                       class="table-card <?php echo $selectedTable === $table ? 'active' : ''; ?>">
                        <div class="table-name"><?php echo htmlspecialchars($table); ?></div>
                        <div class="table-count">
                            <?php echo number_format($stats[$table] ?? 0); ?> records
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Table Data Viewer -->
        <?php if ($selectedTable && !empty($tableData)): ?>
            <div class="table-viewer">
                <div class="table-header">
                    <h2 class="table-title">📄 Table: <?php echo htmlspecialchars($selectedTable); ?></h2>
                    <div>
                        <span style="color: #6b7280; font-size: 0.9rem;">
                            Showing <?php echo count($tableData); ?> of <?php echo number_format($stats[$selectedTable] ?? 0); ?> records
                        </span>
                    </div>
                </div>
                
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <?php foreach ($tableColumns as $column): ?>
                                    <th><?php echo htmlspecialchars($column['Field']); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tableData as $row): ?>
                                <tr>
                                    <?php foreach ($tableColumns as $column): ?>
                                        <td>
                                            <?php 
                                            $value = $row[$column['Field']] ?? '';
                                            if (strlen($value) > 100) {
                                                echo htmlspecialchars(substr($value, 0, 100)) . '...';
                                            } else {
                                                echo htmlspecialchars($value ?: '(empty)');
                                            }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($selectedTable && empty($tableData)): ?>
            <div class="alert alert-error">
                Table "<?php echo htmlspecialchars($selectedTable); ?>" is empty or has no records.
            </div>
        <?php endif; ?>
        
        <!-- Quick Links -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <h3 style="margin-bottom: 15px;">🔗 Quick Links</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="/admin/dashboard.php" class="btn btn-secondary">Dashboard</a>
                <a href="/admin/products.php" class="btn btn-secondary">Products</a>
                <a href="/admin/orders.php" class="btn btn-secondary">Orders</a>
                <a href="/admin/settings.php" class="btn btn-secondary">Settings</a>
                <?php if (file_exists('C:/xampp/phpmyadmin/index.php')): ?>
                    <a href="http://localhost/phpmyadmin" target="_blank" class="btn">Open phpMyAdmin</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>








