<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if ($uri === '/' || $uri === '') {
    $uri = 'index.html';
}

if (file_exists(__DIR__ . '/../_build/' . $uri)) {
    if (str_ends_with($uri, '.css')) {
        header("Content-Type: text/css");
    } else {
        if (str_ends_with($uri, '/')) {
            $uri .= '/index.html';
        }
        header('Content-Type: ' . mime_content_type(__DIR__ . '/../_build/' . $uri));
    }
    echo file_get_contents(__DIR__ . '/../_build/' . $uri);
}
