<?php
/**
 * Backend Root Index Page
 * Redirects to admin panel or shows welcome page
 */

// If accessing root, redirect to admin login
if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php') {
    header('Location: /admin/login.php');
    exit;
}

// Otherwise show a simple welcome page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hamro Digi Cart - Backend</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 32px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .links {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        a {
            display: block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        a:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .info {
            margin-top: 30px;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 8px;
            font-size: 14px;
            color: #666;
        }
        .info strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛒 Hamro Digi Cart</h1>
        <p>Backend Server is Running</p>
        
        <div class="links">
            <a href="/admin/login.php">🔐 Admin Panel</a>
            <a href="/api/products.php">📦 API - Products</a>
            <a href="/api/categories.php">📂 API - Categories</a>
            <a href="/check-extensions.php">🔧 Check Extensions</a>
        </div>
        
        <div class="info">
            <strong>Server Information:</strong><br>
            PHP Version: <?php echo phpversion(); ?><br>
            Server: PHP Built-in Server<br>
            Status: ✅ Running
        </div>
    </div>
</body>
</html>






