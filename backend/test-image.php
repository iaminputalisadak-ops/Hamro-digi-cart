<?php
/**
 * Test image serving
 */
$filename = isset($_GET['file']) ? $_GET['file'] : '6951318330775_1766928771.png';
$uploadsDir = __DIR__ . '/uploads/';
$filePath = $uploadsDir . $filename;

echo "<h1>Image Test</h1>";
echo "<p>Filename: $filename</p>";
echo "<p>Uploads Dir: $uploadsDir</p>";
echo "<p>File Path: $filePath</p>";
echo "<p>File Exists: " . (file_exists($filePath) ? 'YES' : 'NO') . "</p>";
echo "<p>Is File: " . (is_file($filePath) ? 'YES' : 'NO') . "</p>";

if (file_exists($filePath)) {
    echo "<p>File Size: " . filesize($filePath) . " bytes</p>";
    echo "<p>MIME Type: " . mime_content_type($filePath) . "</p>";
    echo "<h2>Image:</h2>";
    echo "<img src='http://localhost:8000/uploads/$filename' alt='Test' style='max-width: 500px;'>";
    echo "<h2>Direct Link:</h2>";
    echo "<a href='http://localhost:8000/uploads/$filename' target='_blank'>Open Image</a>";
} else {
    echo "<h2>Files in uploads directory:</h2>";
    $files = glob($uploadsDir . '*');
    if ($files) {
        echo "<ul>";
        foreach ($files as $file) {
            echo "<li>" . basename($file) . " (" . filesize($file) . " bytes)</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No files found in uploads directory</p>";
    }
}





