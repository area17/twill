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

$base = realpath(__DIR__ . '/../_build/');
if (is_dir($base . $uri)) {
    $uri .= "/index.html";
}
$target = realpath($base . $uri);

if ($target && str_starts_with($target, $base) && file_exists($target)) {
    if (str_ends_with($target, '.css')) {
        header("Content-Type: text/css");
    } else {
        header('Content-Type: ' . mime_content_type($target));
    }
    echo file_get_contents($target);
} else {
    http_response_code(404);
}
