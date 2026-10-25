<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/');
}
if (!valid_csrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('درخواست نامعتبر است. صفحه را دوباره باز کنید.');
}

$input = [
    'first_name' => clean_name((string) ($_POST['first_name'] ?? '')),
    'last_name' => clean_name((string) ($_POST['last_name'] ?? '')),
    'phone' => normalize_phone((string) ($_POST['phone'] ?? '')),
    'national_id' => trim(normalize_digits((string) ($_POST['national_id'] ?? ''))),
    'email' => strtolower(trim((string) ($_POST['email'] ?? ''))),
];
$captchaAnswer = (string) ($_POST['captcha'] ?? '');
$errors = [];

if (!allow_attempt('register', 12, 600)) {
    $errors[] = 'تعداد درخواست‌ها زیاد است. حدود ۱۰ دقیقه دیگر دوباره تلاش کنید.';
}
if (!valid_name($input['first_name'])) {
    $errors[] = 'نام باید بین ۲ تا ۶۰ نویسه و شامل حروف معتبر باشد.';
}
if (!valid_name($input['last_name'])) {
    $errors[] = 'نام خانوادگی باید بین ۲ تا ۶۰ نویسه و شامل حروف معتبر باشد.';
}
if (!preg_match('/^09\d{9}$/D', $input['phone'])) {
    $errors[] = 'شماره موبایل را در قالب 09123456789 وارد کنید.';
}
if (!valid_national_id($input['national_id'])) {
    $errors[] = 'کد ملی واردشده معتبر نیست.';
}
if (strlen($input['email']) > 254 || filter_var($input['email'], FILTER_VALIDATE_EMAIL) === false) {
    $errors[] = 'آدرس ایمیل معتبر نیست.';
}
if (!valid_captcha($captchaAnswer)) {
    $errors[] = 'کد امنیتی اشتباه است یا اعتبار آن تمام شده است.';
}
// Every submit consumes the old captcha, whether accepted or rejected.
new_captcha();
if ($errors !== []) {
    flash('register_errors', $errors);
    flash('register_old', $input);
    redirect('/#registration');
}

try {
    $stmt = db()->prepare('INSERT INTO users (first_name, last_name, phone, national_id, email) VALUES (:first_name, :last_name, :phone, :national_id, :email)');
    $stmt->execute($input);
    flash('register_success', 'ثبت‌نام شما با موفقیت انجام شد.');
    redirect('/#registration');
} catch (PDOException $exception) {
    $message = str_contains($exception->getMessage(), 'UNIQUE constraint failed')
        ? 'این کد ملی یا ایمیل قبلاً ثبت شده است.'
        : 'خطایی در ثبت اطلاعات رخ داد. دوباره تلاش کنید.';
    error_log('Registration failed: ' . $exception->getCode());
    flash('register_errors', [$message]);
    flash('register_old', $input);
    redirect('/#registration');
} catch (Throwable $exception) {
    error_log('Registration error: ' . $exception->getMessage());
    flash('register_errors', ['خطایی در ذخیره اطلاعات رخ داد. تنظیمات دیتابیس را بررسی کنید.']);
    flash('register_old', $input);
    redirect('/#registration');
}
