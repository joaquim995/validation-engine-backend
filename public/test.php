<?php
// Simple test file to check if PHP is working
echo json_encode([
    'status' => 'PHP is working',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION
]);
?>
