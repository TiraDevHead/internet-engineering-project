<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !valid_csrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('درخواست نامعتبر است.');
}
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
redirect('/login.php');
