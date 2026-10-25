<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
ensure_captcha();
$errors = take_flash('register_errors', []);
$old = take_flash('register_old', []);
$success = take_flash('register_success', '');
$pageTitle = 'ثبت‌نام آنلاین | پروژه مهندسی اینترنت';
$activePage = 'registration';
require __DIR__ . '/../src/header.php';
?>
<main class="layout" id="registration">
    <section class="intro-panel" aria-labelledby="intro-title">
        <div class="intro-content">
            <h1 id="intro-title">برای شروع و استفاده از خدمات در سایت ثبت نام کنید</h1>
            <p class="intro-description">فقط چند قدم ساده تا تکمیل ثبت‌نام شما فاصله است. اطلاعات خود را وارد کنید و بقیه کار را به ما بسپارید.</p>
            <div class="feature-list">
                <div class="feature"><span class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m5 12 4 4L19 6"/></svg></span><span>فرآیند سریع و ساده</span></div>
                <div class="feature"><span class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></span><span>ذخیره‌سازی امن اطلاعات</span></div>
                <div class="feature"><span class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 3 7v6c0 5 3.6 7.9 9 9 5.4-1.1 9-4 9-9V7l-9-4Z"/><path d="m9 12 2 2 4-4"/></svg></span><span>تأیید صحت اطلاعات</span></div>
            </div>
        </div>
        <div class="orbit-art" aria-hidden="true"><div class="orbit orbit-outer"></div><div class="orbit orbit-inner"></div><div class="orbit-core"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="4" y="3" width="16" height="18" rx="3"/><path d="M8 9h8M8 13h8M8 17h5"/></svg></div><span class="orb orb-one"></span><span class="orb orb-two"></span></div>
        <p class="intro-foot">طراحی ساده برای تجربه‌ای بهتر <span aria-hidden="true">✦</span></p>
    </section>

    <section class="form-panel" aria-labelledby="form-title">
        <div class="card-top"><span class="step-chip">مرحله ۰۱ از ۰۱</span></div>
        <div class="form-heading"><h2 id="form-title">ایجاد حساب کاربری</h2><p>لطفاً اطلاعات زیر را با دقت تکمیل کنید.</p></div>
        <?php if ($success): ?>
            <div class="alert alert-success toast-notification" role="status" id="success-toast"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg><div><strong>ثبت‌نام موفق!</strong><span><?= e($success) ?></span></div><button type="button" class="alert-close" aria-label="بستن پیام" data-dismiss-alert>×</button></div>
        <?php endif; ?>
        <?php if ($errors): ?>
            <div class="alert alert-error" role="alert"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v6m0 4h.01"/></svg><div><strong>اطلاعات را بررسی کنید</strong><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div></div>
        <?php endif; ?>
        <form action="/register.php" method="post" class="registration-form" id="registration-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" id="csrf-token">
            <div class="form-grid">
                <div class="field"><label for="first_name">نام <span class="required">*</span></label><div class="input-shell"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="3.5"/><path d="M5 21v-2a7 7 0 0 1 14 0v2"/></svg><input id="first_name" name="first_name" type="text" autocomplete="given-name" maxlength="60" required placeholder="مثلاً علی" value="<?= e($old['first_name'] ?? '') ?>"></div></div>
                <div class="field"><label for="last_name">نام خانوادگی <span class="required">*</span></label><div class="input-shell"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="3.5"/><path d="M5 21v-2a7 7 0 0 1 14 0v2"/></svg><input id="last_name" name="last_name" type="text" autocomplete="family-name" maxlength="60" required placeholder="مثلاً رضایی" value="<?= e($old['last_name'] ?? '') ?>"></div></div>
                <div class="field"><label for="phone">شماره موبایل <span class="required">*</span></label><div class="input-shell"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg><input id="phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" maxlength="20" required placeholder="09123456789" dir="ltr" value="<?= e($old['phone'] ?? '') ?>"></div></div>
                <div class="field"><label for="national_id">کد ملی <span class="required">*</span></label><div class="input-shell"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 10h5m-5 4h10"/></svg><input id="national_id" name="national_id" type="text" inputmode="numeric" maxlength="10" required placeholder="کد ملی ۱۰ رقمی" dir="ltr" value="<?= e($old['national_id'] ?? '') ?>"></div></div>
                <div class="field full-width"><label for="email">آدرس ایمیل <span class="required">*</span></label><div class="input-shell"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg><input id="email" name="email" type="email" autocomplete="email" maxlength="254" required placeholder="name@example.com" dir="ltr" value="<?= e($old['email'] ?? '') ?>"></div></div>
            </div>
            <div class="captcha-area"><div class="captcha-head"><label for="captcha">کد امنیتی <span class="required">*</span></label><span>برای تأیید هویت، کد تصویر را وارد کنید.</span></div><div class="captcha-row"><div class="captcha-image-wrap"><img src="/captcha.php" alt="کد امنیتی پنج کاراکتری" id="captcha-image" width="230" height="78"></div><button type="button" class="refresh-button" id="captcha-refresh" aria-label="دریافت کد امنیتی جدید" title="کد جدید"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M20 7v5h-5M4 17v-5h5"/><path d="M5.7 9A7 7 0 0 1 18 6l2 1M4 17l2 1a7 7 0 0 0 12.3-3"/></svg></button><div class="input-shell captcha-input"><input type="text" name="captcha" id="captcha" placeholder="کد تصویر" dir="ltr" maxlength="5" autocomplete="off" required spellcheck="false" aria-label="کد امنیتی"></div></div><span class="field-hint">حروف کوچک و بزرگ تفاوتی ندارند.</span></div>
            <button type="submit" class="primary-button" id="register-submit"><span>تکمیل ثبت‌نام</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M20 12H4m6 6-6-6 6-6"/></svg></button>
            <p class="form-disclaimer"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M12 10v6m0-9h.01"/></svg> با ثبت‌نام، اطلاعات شما در سامانه ذخیره می‌شود و صرفاً مدیر مجاز به مشاهده جزئیات آن است.</p>
        </form>
    </section>
</main>
<?php require __DIR__ . '/../src/footer.php'; ?>
