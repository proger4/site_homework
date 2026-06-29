<?php

declare(strict_types=1);

/**
 * Router script for PHP's built-in development server.
 * Serves static files directly and routes other requests through index.php.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$file = __DIR__ . $uri;

// Serve static files directly if they exist
if ($uri !== '/' && is_file($file)) {
    // Set appropriate Content-Type header based on file extension
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
    ];
    
    if (isset($mimeTypes[$extension])) {
        header('Content-Type: ' . $mimeTypes[$extension]);
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit(0);
    }
    
    // For other static files, let PHP built-in server handle it
    return false;
}

// Otherwise, route through Yii
require __DIR__ . '/index.php';
