<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/functions.php';
session_start();

function check(bool $condition, string $label): void
{
    if (!$condition) {
        fwrite(STDERR, "FAILED: {$label}\n");
        exit(1);
    }
    echo "PASS: {$label}\n";
}

check(valid_national_id('0123456789'), 'Iranian national ID valid / leading zero');
check(valid_national_id('1234567891'), 'Iranian national ID checksum valid');
check(!valid_national_id('1234567890'), 'Iranian national ID bad checksum');
check(!valid_national_id('0000000000'), 'Repeated digits rejected');
check(!valid_national_id('12345'), 'Wrong length rejected');
check(normalize_digits('۰۹۱٢٣٤۵۶۷۸۹') === '09123456789', 'Persian / Arabic digits');
check(normalize_phone('+989123456789') === '09123456789', 'International phone normalized');
check(normalize_phone('0098 912-345-6789') === '09123456789', '0098 phone normalized');
check(valid_name('علی') && valid_name('سارا محمدی') && valid_name('Mary-Jane'), 'Unicode names accepted');
check(!valid_name('علی123') && !valid_name('!'), 'Invalid names rejected');
check(e('<script>alert(1)</script>') === '&lt;script&gt;alert(1)&lt;/script&gt;', 'HTML escaping');
check(mb_substr_safe('علی', 0, 1) === 'ع', 'UTF8 avatar initial');

$token = csrf_token();
check(valid_csrf($token) && !valid_csrf('not-the-real-token'), 'CSRF verification');
new_captcha();
check(strlen($_SESSION['captcha_code']) === 5, 'Captcha generated');
check(valid_captcha(strtolower($_SESSION['captcha_code'])), 'Captcha ignores letter case');
check(!valid_captcha('XXXXX0'), 'Captcha wrong answer');
$_SESSION['captcha_expires'] = time() - 1;
check(!valid_captcha($_SESSION['captcha_code']), 'Captcha expiration');
check(allow_attempt('test', 2, 600) && allow_attempt('test', 2, 600) && !allow_attempt('test', 2, 600), 'Rate limiter');
echo "\nAll smoke tests passed.\n";
