<?php

/**
 * Router script for PHP built-in server when using Laravel
 */

// This file directs all requests to index.php
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Check if the file exists in the public directory
    if ($path !== '/' && file_exists(__DIR__ . $path)) {
        // Serve the requested resource as-is.
        return false;
    }
}

// Forward everything else to index.php
require_once __DIR__ . '/index.php';
