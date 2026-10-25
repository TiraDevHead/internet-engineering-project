<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_admin();
$user = null;
$error = '';
$searched = false;
$nationalId = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!valid_csrf($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('درخواست نامعتبر است.');
    }
    $nationalId = trim(normalize_digits((string) ($_POST['national_id'] ?? '')));
    if (!allow_attempt('search', 100, 900)) {
        $error = 'تعداد جست‌وجوها بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.';
    } elseif (!valid_national_id($nationalId)) {
        $error = 'یک کد ملی ۱۰ رقمی معتبر وارد کنید.';
    } else {
        try {
            $stmt = db()->prepare('SELECT first_name, last_name, national_id, phone, email, created_at FROM users WHERE national_id = :national_id LIMIT 1');
            $stmt->execute(['national_id' => $nationalId]);
            $user = $stmt->fetch() ?: null;
            $searched = true;
        } catch (Throwable $exception) {
            error_log('Search error: ' . $exception->getMessage());
            $error = 'جست‌وجو ممکن نشد. لطفاً اتصال به دیتابیس را بررسی کنید.';
        }
    }
}
$pageTitle = 'جست‌وجوی کاربران | پروژه مهندسی اینترنت';
$activePage = 'search';
require __DIR__ . '/../src/header.php';
?>
<main class="search-layout"><div class="search-header"><span class="step-chip">داشبورد مدیریت</span><h1>جست‌وجوی کاربران</h1><p>برای مشاهده اطلاعات ثبت‌شده، کد ملی کاربر موردنظر را وارد کنید.</p></div>
<section class="search-card"><form class="search-form" action="/search.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="field"><label for="search_national_id">کد ملی کاربر</label><div class="input-shell search-input-shell"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="10" cy="10" r="6"/><path d="m15 15 6 6"/></svg><input id="search_national_id" name="national_id" type="text" inputmode="numeric" dir="ltr" maxlength="10" required placeholder="کد ملی ۱۰ رقمی را وارد کنید" value="<?= e($nationalId) ?>"></div></div><button class="primary-button search-submit" type="submit">جست‌وجو <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="10" cy="10" r="6"/><path d="m15 15 6 6"/></svg></button></form></section>
<?php if ($error): ?><div class="alert alert-error search-message" role="alert"><?= e($error) ?></div><?php elseif ($user): ?>
<section class="result-card" aria-label="اطلاعات کاربر"><div class="result-title"><div><span class="result-dot"></span><h2>اطلاعات کاربر یافت شد</h2></div><span class="result-badge">ثبت‌نام‌شده</span></div><div class="result-profile"><div class="avatar"><?= e(mb_substr_safe($user['first_name'], 0, 1)) ?></div><div><h3><?= e($user['first_name'] . ' ' . $user['last_name']) ?></h3><p>اطلاعات کامل حساب کاربری</p></div></div><div class="result-grid"><div class="data-cell"><span>نام</span><strong><?= e($user['first_name']) ?></strong></div><div class="data-cell"><span>نام خانوادگی</span><strong><?= e($user['last_name']) ?></strong></div><div class="data-cell"><span>کد ملی</span><strong dir="ltr"><?= e($user['national_id']) ?></strong></div><div class="data-cell"><span>شماره موبایل</span><strong dir="ltr"><?= e($user['phone']) ?></strong></div><div class="data-cell"><span>ایمیل</span><strong dir="ltr" class="email-result"><?= e($user['email']) ?></strong></div><div class="data-cell"><span>تاریخ ثبت‌نام (تهران)</span><strong><?= e(format_iran_time($user['created_at'])) ?></strong></div></div></section>
<?php elseif ($searched): ?><section class="empty-result"><div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="10" cy="10" r="6"/><path d="m15 15 6 6M7 10h6"/></svg></div><h2>کاربری یافت نشد</h2><p>برای کد ملی واردشده اطلاعاتی ثبت نشده است.</p></section>
<?php else: ?><section class="empty-result empty-initial"><div class="empty-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="3" width="16" height="18" rx="3"/><circle cx="12" cy="10" r="3"/><path d="M8 17c0-3 8-3 8 0"/></svg></div><h2>آماده جست‌وجو</h2><p>نتیجه جست‌وجو و مشخصات کامل کاربر اینجا نمایش داده می‌شود.</p></section><?php endif; ?>
<div class="privacy-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3 3 7v6c0 5 3.6 7.9 9 9 5.4-1.1 9-4 9-9V7l-9-4Z"/><path d="m9 12 2 2 4-4"/></svg> این صفحه فقط در اختیار مدیران مجاز سامانه است.</div>
</main>
<?php require __DIR__ . '/../src/footer.php'; ?>
