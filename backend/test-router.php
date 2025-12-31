<?php
/**
 * Test if router is being called
 */
echo "Router Test\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . "\n";
echo "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'not set') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'not set') . "\n";
echo "__DIR__: " . __DIR__ . "\n";
echo "Router file exists: " . (file_exists(__DIR__ . '/router.php') ? 'YES' : 'NO') . "\n";





