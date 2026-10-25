<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !valid_csrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    echo json_encode(['ok' => false]);
    exit;
}
if (!allow_attempt('refresh', 30, 600)) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'message' => 'تعداد تلاش‌ها بیش از حد مجاز است.']);
    exit;
}
new_captcha();
echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
