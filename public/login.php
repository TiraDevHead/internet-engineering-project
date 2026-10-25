<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
if (is_admin()) {
    redirect('/search.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('درخواست نامعتبر است.');
    }
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if (!allow_attempt('login', 8, 900)) {
        flash('login_error', 'تلاش‌های ورود بیش از حد مجاز است. ۱۵ دقیقه دیگر تلاش کنید.');
        redirect('/login.php');
    }
    try {
        $stmt = db()->prepare('SELECT id, username, password_hash FROM admins WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            unset($_SESSION['csrf_token'], $_SESSION['rates']['login']);
            redirect('/search.php');
        }
        flash('login_error', 'نام کاربری یا رمز عبور نادرست است.');
    } catch (Throwable $exception) {
        error_log('Admin login failed: ' . $exception->getMessage());
        flash('login_error', 'ارتباط با دیتابیس برقرار نشد. تنظیمات را بررسی کنید.');
    }
    redirect('/login.php');
}
$pageTitle = 'ورود مدیر | پروژه مهندسی اینترنت';
$activePage = '';
$error = take_flash('login_error', '');
require __DIR__ . '/../src/header.php';
?>
<main class="simple-layout"><section class="auth-card" aria-labelledby="auth-title"><div class="auth-symbol"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></div><span class="step-chip">پنل مدیریت</span><h1 id="auth-title">خوش آمدید، مدیر عزیز</h1><p class="auth-subtitle">برای جست‌وجوی اطلاعات کاربران وارد حساب مدیریتی شوید.</p>
<?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="auth-form" action="/login.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="field"><label for="username">نام کاربری</label><div class="input-shell"><input name="username" id="username" type="text" required autocomplete="username" placeholder="نام کاربری مدیر" dir="ltr" maxlength="80"></div></div><div class="field"><label for="password">رمز عبور</label><div class="input-shell password-shell"><input name="password" id="password" type="password" required autocomplete="current-password" placeholder="رمز عبور خود را وارد کنید" dir="ltr"><button type="button" class="show-password" data-toggle-password aria-label="نمایش رمز عبور"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg></button></div></div><button type="submit" class="primary-button"><span>ورود به پنل مدیریت</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M20 12H4m6 6-6-6 6-6"/></svg></button></form><p class="auth-footnote">مدیر هنوز ساخته نشده؟ فایل راهنمای README.md را برای راه‌اندازی بخوانید.</p></section></main>
<?php require __DIR__ . '/../src/footer.php'; ?>
