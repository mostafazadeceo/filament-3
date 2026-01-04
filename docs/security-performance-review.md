# ارزیابی امنیت و کارایی (نهایی)

## امنیت
- وبهوک‌ها: امضا + raw body + idempotency فعال است.
- Host header: TrustedHosts/TrustedProxies و allowlist فعال.
- Tenancy isolation: scope در مدل‌ها و AccessService فعال.
- Sanitization: PageBuilder/CMS sanitization فعال.
- Rate limiting: مسیرهای حساس (دامنه/وبهوک/API) محدود شده‌اند.

## کارایی
- استفاده از pagination در لیست‌ها.
- کاهش N+1 با eager loading در منابع اصلی.
- عملیات سنگین در jobها (Provider sync, webhook deliveries).

## مسیرهای بررسی
- اجرای `php artisan test`
- اجرای `./scripts/qa-sanity.sh`
- اجرای `./scripts/regression-smoke.sh`
