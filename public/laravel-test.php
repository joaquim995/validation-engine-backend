<?php
// Laravel bootstrap test
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo json_encode([
        'status' => 'Laravel bootstrap successful',
        'app_env' => env('APP_ENV'),
        'has_app_key' => !empty(env('APP_KEY')),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'Laravel bootstrap failed',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
