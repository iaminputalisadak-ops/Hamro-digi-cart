<?php
/**
 * Test Image Loading
 */
header('Content-Type: text/html; charset=utf-8');
$testFile = '6951318330775_1766928771.png';
$testUrl = 'http://localhost:8000/uploads/' . $testFile;
$filePath = __DIR__ . '/uploads/' . $testFile;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Image Load Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; }
        .error { background: #f8d7da; }
        img { max-width: 500px; border: 2px solid #333; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Image Load Test</h1>
    
    <div class="test">
        <h2>File Check</h2>
        <p>File path: <?php echo htmlspecialchars($filePath); ?></p>
        <p>File exists: <?php echo file_exists($filePath) ? 'YES ✓' : 'NO ✗'; ?></p>
        <p>Is file: <?php echo is_file($filePath) ? 'YES ✓' : 'NO ✗'; ?></p>
        <?php if (file_exists($filePath)): ?>
            <p>File size: <?php echo number_format(filesize($filePath)); ?> bytes</p>
        <?php endif; ?>
    </div>
    
    <div class="test">
        <h2>Image Display Test</h2>
        <p>Image URL: <a href="<?php echo $testUrl; ?>" target="_blank"><?php echo $testUrl; ?></a></p>
        <p>Try to load image:</p>
        <img src="<?php echo $testUrl; ?>" 
             alt="Test Image" 
             onerror="this.style.border='5px solid red'; this.alt='FAILED TO LOAD'; console.error('Image failed to load');"
             onload="this.style.border='5px solid green'; console.log('Image loaded successfully');">
    </div>
    
    <div class="test">
        <h2>Direct Router Test</h2>
        <?php
        // Simulate what router does
        $uploadsDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
        $routerFilePath = $uploadsDir . $testFile;
        $routerFilePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $routerFilePath);
        
        if (file_exists($routerFilePath) && is_file($routerFilePath)) {
            echo '<p class="success">✓ Router logic: File found</p>';
            $realPath = realpath($routerFilePath);
            $realDir = realpath($uploadsDir);
            if ($realPath && $realDir) {
                $isSecure = stripos($realPath, $realDir) === 0;
                echo '<p>Security check: ' . ($isSecure ? 'PASS ✓' : 'FAIL ✗') . '</p>';
                echo '<p>Real file path: ' . htmlspecialchars($realPath) . '</p>';
                echo '<p>Real uploads dir: ' . htmlspecialchars($realDir) . '</p>';
            }
        } else {
            echo '<p class="error">✗ Router logic: File not found</p>';
        }
        ?>
    </div>
</body>
</html>





