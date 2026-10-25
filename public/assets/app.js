'use strict';
(() => {
  const digitMap = {'۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9','٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'};
  const toEnglishDigits = value => value.replace(/[۰-۹٠-٩]/g, char => digitMap[char]);

  document.querySelectorAll('input[name="national_id"], input[name="phone"], input[name="captcha"]').forEach(input => {
    input.addEventListener('input', () => {
      const replaced = toEnglishDigits(input.value);
      if (replaced !== input.value) input.value = replaced;
    });
  });

  const refresh = document.querySelector('#captcha-refresh');
  const captchaImage = document.querySelector('#captcha-image');
  const csrf = document.querySelector('#csrf-token');
  if (refresh && captchaImage && csrf) {
    refresh.addEventListener('click', async () => {
      refresh.disabled = true;
      try {
        const response = await fetch('/refresh-captcha.php', {
          method: 'POST',
          credentials: 'same-origin',
          headers: {'Content-Type':'application/x-www-form-urlencoded'},
          body: new URLSearchParams({csrf_token: csrf.value}).toString()
        });
        if (!response.ok) throw new Error('Captcha refresh failed');
        captchaImage.src = `/captcha.php?v=${Date.now()}`;
        document.querySelector('#captcha')?.focus();
      } catch (_) {
        window.alert('دریافت کد جدید ناموفق بود. لطفاً صفحه را تازه‌سازی کنید.');
      } finally {
        refresh.disabled = false;
      }
    });
  }

  document.querySelectorAll('[data-dismiss-alert]').forEach(button => {
    button.addEventListener('click', () => {
      const alert = button.closest('.alert');
      if (!alert) return;
      alert.classList.add('is-hiding');
      window.setTimeout(() => alert.remove(), 300);
    });
  });

  document.querySelectorAll('[data-toggle-password]').forEach(button => {
    button.addEventListener('click', () => {
      const input = button.parentElement.querySelector('input');
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
      button.setAttribute('aria-label', input.type === 'password' ? 'نمایش رمز عبور' : 'پنهان کردن رمز عبور');
    });
  });
})();
