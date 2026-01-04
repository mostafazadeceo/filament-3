# Changelog

تمام تغییرات قابل توجه این پروژه در این فایل مستندسازی می‌شود.

این پروژه از [Semantic Versioning](https://semver.org/spec/v2.0.0.html) پیروی می‌کند و قالب [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) را رعایت می‌کند.

## [Unreleased]
### Added
- [ASSUMPTION] هر ماژول جدید باید مستندات SPEC/INSTALL/API و capabilityهای IAM را اضافه کند.
- یکتاسازی وبهوک‌های پرداخت بر اساس tenant/provider/external_id برای جلوگیری از تداخل بین tenantها.
- تست‌های جدید برای وبهوک‌های پرداخت و پایداری صف آفلاین PWA.
- ثبت دستگاه و توکن FCM در اپ اندروید برای اعلان‌ها.
- ذخیره‌سازی تغییرات sync دریافتی در اپ اندروید.
- Play Integrity token در ورود اندروید (قابل فعال‌سازی با feature flag).

### Changed
- [ASSUMPTION] مستندات مرجع در `docs/_reference` به‌عنوان منبع حقیقت تثبیت شد.
- API نرخ ارز اکنون نیازمند توکن است و نرخ درخواست قابل تنظیم دارد.
- Webhook handler پرداخت‌ها اکنون tenant context را اعمال می‌کند.
- همگام‌سازی PWA در خطاها وضعیت outbox را به failed برمی‌گرداند و از گیرکردن جلوگیری می‌کند.
- Web/PWA: تنظیم صریح outputFileTracingRoot برای حذف هشدار lockfile.

### Fixed
- جلوگیری از تداخل رویداد وبهوک بین tenantها.
- جلوگیری از گیرکردن آیتم‌های outbox در وضعیت syncing هنگام خطای شبکه.
- اعمال scope مشاهده برای OpenAPI در ماژول‌های restaurant-ops، petty-cash و payroll-attendance.

## [1.0.0] - 2026-01-03
### Added
- پلتفرم ماژولار Filament v4 با پنل Admin و Tenant و پلاگین‌های متعدد (Commerce، Workhub، Payments، Providers، CMS، Blog، Loyalty، Crypto و ...).
- IAM Suite چند‌مستاجری با مدل‌های Tenant/Role/Permission/Wallet/Subscription و وبهوک‌های مرکزی.
- استک اعلان‌ها (Core + کانال‌های Telegram/WhatsApp/SMS/Bale/WebPush) به‌عنوان زیرساخت اطلاع‌رسانی.
- APIهای نسخه‌بندی‌شده `/api/v1/*` همراه با middlewareهای امنیتی و scoped permissions.
- مستندات مرجع و Runbook عملیاتی برای دیپلوی، rollback و incident response.

### Security
- امضای وبهوک‌ها و کنترل idempotency برای جلوگیری از replay/duplication.

[1.0.0]: ./RELEASE_NOTES/1.0.0.md
