# Filament Chat — INSTALL

## پیش نیازها
- Rocket.Chat روی یک VM مشترک نصب شده باشد (برای همه سازمان‌ها).
- ایجاد یک Admin User و ساخت Personal Access Token (PAT).
- اگر 2FA فعال است، PAT باید با گزینه bypass/ignore 2FA ساخته شود تا API های مدیریتی کار کنند.
- فعال بودن OIDC در Rocket.Chat و تنظیم اتصال به IdP (OIDC).

## نصب پکیج (لوکال)
- پکیج در `packages/filament-chat` موجود است.
- ثبت در `composer.json` انجام شده است.
- مهاجرت ها را اجرا کنید:
  - `php artisan migrate --force`

## تنظیم اتصال
1. در پنل مدیریت (Admin یا Tenant)، بخش «اتصال های چت» را باز کنید.
2. یک اتصال جدید بسازید:
   - Base URL: `https://chat.<domain>` (همه tenantها به همین آدرس مشترک اشاره می‌کنند)
   - Admin User Id و Admin Token (PAT)
3. «آزمون اتصال» را اجرا کنید.
4. «همگام سازی کاربران» را اجرا کنید (برای هر tenant تیم و اتاق پیش‌فرض ساخته می‌شود).

## OIDC
- OIDC در Rocket.Chat باید به IdP شما متصل شود (OIDC Provider).
- در هاب، OIDC provider داخلی فعال شده و آدرس Discovery برابر است با:
  - `/.well-known/openid-configuration`
- مقادیر `Issuer`, `Client ID`, `Client Secret` در فرم اتصال ذخیره می شوند و در صورت خالی بودن به صورت خودکار تولید می گردند.

### تنظیم Rocket.Chat
1. در Rocket.Chat بخش OAuth/OIDC را باز کنید.
2. `Issuer / Discovery URL` را برابر آدرس هاب بگذارید.
3. `Client ID` و `Client Secret` را از اتصال چت بردارید.
4. `Redirect URI` را مطابق مسیر Rocket.Chat تنظیم کنید و همان را در `Redirect URIs` اتصال ثبت کنید.

### متغیرهای محیطی پیشنهادی (هاب)
```
FILAMAT_IAM_SSO_ENABLED=true
FILAMAT_IAM_FEATURE_SSO=true
FILAMAT_IAM_OIDC_ISSUER=https://hub.abrak.org
FILAMAT_IAM_OIDC_LOGIN_URL=/tenant/{tenant}/login
```

### نقش‌ها و دسترسی‌ها (Rocket.Chat)
- نقش‌های IAM به نقش‌های Rocket.Chat نگاشت می‌شوند (قابل تنظیم در اتصال چت).
- پیش‌فرض: `tenant_owner => owner` و سایر نقش‌ها به `user` می‌روند.
- اگر می‌خواهید فقط مالک سازمان بتواند اتاق بسازد، در Rocket.Chat مجوز ساخت اتاق را فقط برای نقش موردنظر فعال کنید.
