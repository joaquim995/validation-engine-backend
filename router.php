<?php

/**
 * Laravel router script for PHP built-in server
 * This routes all requests through Laravel's public/index.php
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

// Route everything else through Laravel
require_once __DIR__.'/public/index.php';
