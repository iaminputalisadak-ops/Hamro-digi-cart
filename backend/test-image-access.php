<?php
/**
 * Test Image Access
 */
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Image Access Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
        img { max-width: 300px; margin: 10px 0; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Image Access Test</h1>
    
    <?php
    $uploadsDir = __DIR__ . '/uploads/';
    echo '<div class="test">';
    echo '<h2>Uploads Directory</h2>';
    echo '<p>Path: ' . htmlspecialchars($uploadsDir) . '</p>';
    echo '<p>Exists: ' . (is_dir($uploadsDir) ? 'YES ✓' : 'NO ✗') . '</p>';
    echo '<p>Writable: ' . (is_writable($uploadsDir) ? 'YES ✓' : 'NO ✗') . '</p>';
    echo '</div>';
    
    // List files
    $files = [];
    if (is_dir($uploadsDir)) {
        $files = array_filter(scandir($uploadsDir), function($file) use ($uploadsDir) {
            return $file !== '.' && $file !== '..' && is_file($uploadsDir . $file);
        });
    }
    
    echo '<div class="test">';
    echo '<h2>Files in uploads/</h2>';
    if (empty($files)) {
        echo '<p class="error">No files found in uploads directory</p>';
    } else {
        echo '<p>Found ' . count($files) . ' file(s):</p>';
        echo '<ul>';
        foreach (array_slice($files, 0, 10) as $file) {
            $filePath = $uploadsDir . $file;
            $fileUrl = 'http://localhost:8000/uploads/' . urlencode($file);
            $fileSize = filesize($filePath);
            $fileExists = file_exists($filePath);
            
            echo '<li>';
            echo '<strong>' . htmlspecialchars($file) . '</strong> ';
            echo '(' . number_format($fileSize) . ' bytes) ';
            echo '<a href="' . $fileUrl . '" target="_blank">Test URL</a>';
            echo '</li>';
            
            // Test if image
            if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
                echo '<div style="margin: 10px 0;">';
                echo '<p>Image Test:</p>';
                echo '<img src="' . $fileUrl . '" alt="' . htmlspecialchars($file) . '" ';
                echo 'onerror="this.style.border=\'3px solid red\'; this.alt=\'FAILED TO LOAD\';" ';
                echo 'onload="this.style.border=\'3px solid green\';">';
                echo '</div>';
            }
        }
        echo '</ul>';
    }
    echo '</div>';
    
    // Test router
    echo '<div class="test">';
    echo '<h2>Router Test</h2>';
    if (!empty($files)) {
        $testFile = $uploadsDir . reset($files);
        $testUrl = 'http://localhost:8000/uploads/' . urlencode(reset($files));
        echo '<p>Testing: <a href="' . $testUrl . '" target="_blank">' . htmlspecialchars($testUrl) . '</a></p>';
        echo '<p>File exists: ' . (file_exists($testFile) ? 'YES ✓' : 'NO ✗') . '</p>';
    }
    echo '</div>';
    ?>
    
    <div class="test">
        <h2>Instructions</h2>
        <ol>
            <li>Check if files are listed above</li>
            <li>Click "Test URL" links to see if images load</li>
            <li>If images don't load, check browser console for errors</li>
            <li>Verify the router.php is being used by the server</li>
        </ol>
    </div>
</body>
</html>





