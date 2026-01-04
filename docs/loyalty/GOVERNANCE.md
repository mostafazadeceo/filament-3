# Governance & Privacy

## Consent & Marketing
- کانال‌های پیام‌رسانی فقط با رضایت فعال می‌شوند (`*_opt_in` + timestamp).
- داده‌های کانال از فیلدهای جداگانه ذخیره می‌شود و قابل غیرفعال‌سازی است.

## Data Minimization
- فقط PII لازم (phone/email) ذخیره می‌شود.
- `external_refs` برای اتصال سیستم‌های بیرونی استفاده می‌شود.

## Auditability
- عملیات حساس (تعدیل دستی، تغییر سطح، مشکوک‌سازی) در `loyalty_audit_events` ثبت می‌شود.
- جدول ممیزی قابل ویرایش نیست.

## Retention
- دوره نگهداری audit/events/fraud از طریق `filament-loyalty-club.retention` تنظیم می‌شود.
- داده‌های قدیمی با job دوره‌ای پاکسازی می‌شوند (اگر فعال شود).

## Anti-Fraud
- جلوگیری از self-referral و سقف معرفی در بازه زمانی.
- سیگنال‌های مشکوک در `loyalty_fraud_signals` ثبت و بررسی می‌شوند.

## AI (اختیاری)
- AI فقط پیشنهاددهنده است و بدون فعال‌سازی صریح اجرا نمی‌شود.
- خروجی AI لاگ و در صورت نیاز redaction می‌شود.
