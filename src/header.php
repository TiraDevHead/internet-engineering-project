<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f9ff">
    <meta name="description" content="سامانه ساده و امن ثبت‌نام و مدیریت اطلاعات کاربران">
    <title><?= e($pageTitle ?? 'سامانه ثبت‌نام پروژه مهندسی اینترنت') ?></title>
    <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/styles.css">
    <script defer src="/assets/app.js"></script>
</head>
<body>
<div class="page-glow page-glow-one" aria-hidden="true"></div><div class="page-glow page-glow-two" aria-hidden="true"></div>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="/" aria-label="پروژه مهندسی اینترنت - صفحه اصلی"><span class="brand-icon"><svg viewBox="0 0 30 30" fill="none"><path d="M15 3 27 10v10L15 27 3 20V10L15 3Z" fill="currentColor" opacity=".2"/><path d="m10 15 3.3 3.3L20 11" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span>پروژه <span class="brand-accent">مهندسی اینترنت</span></span></a>
        <nav class="nav-links" aria-label="ناوبری اصلی"><a class="nav-link <?= ($activePage ?? '') === 'registration' ? 'active' : '' ?>" href="/">ثبت‌نام</a><a class="nav-link <?= ($activePage ?? '') === 'search' ? 'active' : '' ?>" href="/search.php">جست‌وجوی کاربران</a></nav>
        <div class="header-action"><?php if (is_admin()): ?><span class="admin-name">مدیر: <?= e($_SESSION['admin_username'] ?? '') ?></span><form action="/logout.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="header-button subtle" type="submit">خروج</button></form><?php else: ?><a class="header-button" href="/login.php">ورود مدیر <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 17 5 12l5-5m-5 5h14"/></svg></a><?php endif; ?></div>
    </div>
</header>
