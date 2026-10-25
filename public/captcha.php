<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
ensure_captcha();
header('Content-Type: image/svg+xml; charset=utf-8');
$code = (string) $_SESSION['captcha_code'];
$lines = '';
for ($i = 0; $i < 9; $i++) {
    $x1 = random_int(0, 230);
    $y1 = random_int(0, 78);
    $x2 = random_int(0, 230);
    $y2 = random_int(0, 78);
    $lines .= '<path d="M' . $x1 . ' ' . $y1 . ' L' . $x2 . ' ' . $y2 . '" stroke="#acc8e8" stroke-width="1" opacity=".55"/>';
}
$letters = '';
foreach (str_split($code) as $i => $letter) {
    $x = 26 + 40 * $i;
    $y = 51 + random_int(-6, 6);
    $rotation = random_int(-13, 13);
    $letters .= '<text x="' . $x . '" y="' . $y . '" transform="rotate(' . $rotation . ' ' . $x . ' ' . $y . ')" font-family="Verdana, sans-serif" font-weight="800" font-size="32" fill="#17498c">' . e($letter) . '</text>';
}
echo '<svg xmlns="http://www.w3.org/2000/svg" width="230" height="78" viewBox="0 0 230 78" role="img" aria-label="کد امنیتی"><rect width="230" height="78" rx="12" fill="#eaf3ff"/><path d="M0 65 Q115 -25 230 34" stroke="#d4e5ff" stroke-width="13" fill="none"/>' . $lines . $letters . '</svg>';
