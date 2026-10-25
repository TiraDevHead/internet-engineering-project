<?php
declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function normalize_digits(string $value): string
{
    return strtr($value, [
        '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
        '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
        '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
    ]);
}

function clean_name(string $value): string
{
    return trim((string) preg_replace('/\s+/u', ' ', $value));
}

function valid_name(string $value): bool
{
    // Persian / Latin letters, diacritics, ZWNJ, spaces, apostrophe and hyphen.
    return preg_match('/^[\p{L}\p{M}\x{200C} \'-]{2,60}$/u', $value) === 1;
}

function valid_national_id(string $value): bool
{
    if (preg_match('/^[0-9]{10}$/D', $value) !== 1 || preg_match('/^(\d)\1{9}$/D', $value) === 1) {
        return false;
    }
    $sum = 0;
    for ($i = 0; $i < 9; $i++) {
        $sum += (int) $value[$i] * (10 - $i);
    }
    $remainder = $sum % 11;
    $checkDigit = $remainder < 2 ? $remainder : 11 - $remainder;
    return $checkDigit === (int) $value[9];
}

function normalize_phone(string $value): string
{
    $value = preg_replace('/[\s\-()]/u', '', normalize_digits($value));
    if (!is_string($value)) {
        return '';
    }
    if (str_starts_with($value, '+98')) {
        $value = '0' . substr($value, 3);
    } elseif (str_starts_with($value, '0098')) {
        $value = '0' . substr($value, 4);
    } elseif (str_starts_with($value, '98') && strlen($value) === 12) {
        $value = '0' . substr($value, 2);
    }
    return $value;
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function valid_csrf(mixed $token): bool
{
    return is_string($token) && hash_equals(csrf_token(), $token);
}

function redirect(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit;
}

function flash(string $key, mixed $value): void
{
    $_SESSION['flash'][$key] = $value;
}

function take_flash(string $key, mixed $default = null): mixed
{
    $value = $_SESSION['flash'][$key] ?? $default;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function new_captcha(): void
{
    $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < 5; $i++) {
        $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }
    $_SESSION['captcha_code'] = $code;
    $_SESSION['captcha_expires'] = time() + 600;
}

function ensure_captcha(): void
{
    if (!isset($_SESSION['captcha_code'], $_SESSION['captcha_expires']) || time() > (int) $_SESSION['captcha_expires']) {
        new_captcha();
    }
}

function valid_captcha(string $answer): bool
{
    return isset($_SESSION['captcha_code'], $_SESSION['captcha_expires'])
        && time() <= (int) $_SESSION['captcha_expires']
        && hash_equals((string) $_SESSION['captcha_code'], strtoupper(trim(normalize_digits($answer))));
}

function allow_attempt(string $key, int $max, int $windowSeconds): bool
{
    $now = time();
    $attempts = $_SESSION['rates'][$key] ?? [];
    $attempts = array_values(array_filter($attempts, static fn ($t) => $t > $now - $windowSeconds));
    if (count($attempts) >= $max) {
        $_SESSION['rates'][$key] = $attempts;
        return false;
    }
    $attempts[] = $now;
    $_SESSION['rates'][$key] = $attempts;
    return true;
}

function is_admin(): bool
{
    return isset($_SESSION['admin_id']) && is_int($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect('/login.php');
    }
}

function format_iran_time(string $utc): string
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $utc, new DateTimeZone('UTC'));
    return $date ? $date->setTimezone(new DateTimeZone('Asia/Tehran'))->format('Y/m/d - H:i') : $utc;
}

function mb_substr_safe(string $value, int $offset, int $length): string
{
    preg_match_all('/./us', $value, $matches);
    return implode('', array_slice($matches[0], $offset, $length));
}
