<?php
declare(strict_types=1);

// Used only with PHP's built-in development server.
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = realpath(__DIR__ . $path);
$public = realpath(__DIR__);
if ($file && str_starts_with($file, $public . DIRECTORY_SEPARATOR) && is_file($file)) {
    if (str_starts_with(basename($file), '.')) {
        http_response_code(404);
        exit('Not found');
    }
    return false;
}
if ($path === '/') {
    require __DIR__ . '/index.php';
    return true;
}
http_response_code(404);
header('Content-Type: text/plain; charset=utf-8');
echo 'صفحه موردنظر پیدا نشد.';
