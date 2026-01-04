# وضعیت فعلی پروژه (AUTOPILOT)

## PRهای تکمیل‌شده
- PR-001: ممیزی اولیه و نقشه وابستگی‌ها
- PR-002: i18n/RTL/جلالی + نمایش زمان ایران
- PR-003: رجیستری افزونه‌ها + چرخه عمر
- PR-004: Feature Gates + اتصال به اشتراک‌ها
- PR-005: Tenancy Domains + Host Resolver (ساب‌دامین MVP)
- PR-006: Site Builder Core + انتشار
- PR-007: Theme Engine + relograde-v1
- PR-008: Page Builder + Sanitization
- PR-009: Content CMS + Blog
- PR-010: Commerce Catalog
- PR-011: Commerce Checkout + Orders (Wallet)
- PR-012: Payments Orchestrator + Webhooks امن
- PR-013: Providers Core + Relograde Adapter
- PR-014: Observability + CI + Docker Compose + Demo
- PR-015: تثبیت demo-e2e + تکمیل چک‌لیست انتشار
- PR-016: UI رجیستری افزونه‌ها + ثبت correlation_id/triggered_by_user_id
- PR-017: پوشش تستی Feature Gates (time windows + limits)
- PR-018: به‌روزرسانی مستندات و برنامه انتشار
- PR-019: برنامه rollback + runbook
- PR-020: API/UI تأیید دامنه سفارشی + rate limit + گزارش وضعیت
- PR-021: TLS automation hooks + وضعیت صدور/تمدید + runbook
- PR-022: سخت‌سازی Host (TrustedHosts/TrustedProxies) + تست‌ها
- PR-023: ادغام Checkout با کاهش موجودی (Inventory Issue Docs)
- PR-024: آداپتر HMAC درگاه tenant + تست‌ها
- PR-025: UI لاگ‌های Provider + بازپردازش dead-letter
- PR-026: همگام‌سازی نهایی مستندات (ERD/Workflows/Specs)
- PR-027: سناریوهای deep-runner (checkout/inventory + HMAC + TLS)
- PR-028: Runbook استیجینگ + تثبیت CI برای سناریوهای E2E
- PR-029: ممیزی رگرسیون + اسکریپت smoke
- PR-030: ممیزی Feature Gates با تست AccessService
- PR-031: تثبیت QA با اسکریپت sanity
- PR-032: یادداشت‌های تحویل نهایی
- PR-033: کنترل نهایی چک‌لیست انتشار
- PR-034: ارزیابی امنیت و کارایی
- PR-035: تثبیت خروجی نهایی مستندات
- PR-036: چک‌لیست رگرسیون ERP
- PR-037: تثبیت فایل‌های وضعیت و بک‌لاگ
- PR-038: چک‌لیست استیجینگ
- PR-039: یادداشت‌های استقرار
- PR-040: تایید نهایی QA
- PR-041: امضای نهایی انتشار
- PR-042: بسته نهایی خروجی
- PR-043: اعلان فریز تغییرات
- PR-044: نسخه نهایی مستندات تحویل
- PR-045: پایان‌بندی نهایی
- PR-046: تایید نهایی چک‌لیست انتشار
- PR-047: اصلاح ResolveTenantFromHost برای نبود جدول site_domains
- PR-048: پاکسازی لاگ‌ها + محافظت رجیستری افزونه‌ها
- PR-049: اصلاح ناسازگاری Filament Actions + سازگاری مهاجرت‌ها با sqlite + اجرای مهاجرت‌ها و تست کامل
- PR-050: دسترسی سوپرادمین برای ایجاد/ویرایش + رفع نبود دکمه‌های ساخت
- PR-051: فعالسازی دکمه ایجاد در لیست‌ها (ListRecordsWithCreate)
- PR-052: یکپارچه‌سازی eSIM Go Provider + سناریوهای deep runner
- PR-055: پایداری IAM در نبود جدول‌ها + اصلاح اعلان وب‌پوش + گروه‌بندی محصولات eSIM + جلوگیری از خطای منابع تجاری
- PR-053: تثبیت Deep Scenario (tap fixes + fake modes for eSIM/Mailtrap/Payments)
- PR-053: رفع خطاهای ThreeCX/PettyCash + انتشار assets Livewire + بهبود همگام‌سازی eSIM Go

## پکیج‌های موجود
- platform-core, feature-gates, tenancy-domains, site-builder-core, theme-engine, page-builder
- content-cms, blog, commerce-catalog, commerce-checkout, commerce-orders, payments-orchestrator
- providers-core, observability
- providers-esim-go-core, providers-esim-go-commerce, providers-esim-go-webhooks, filament-providers-esim-go
- filament-relograde, filament-workhub, filament-accounting-ir, filament-payroll-attendance-ir
- filament-restaurant-ops, filament-petty-cash-ir, filament-currency-rates
- filament-notify-* (core + channels), filamat-iam-suite

## موارد ناقص نسبت به معماری
- تست همزمانی (parallel checkout) برای رزرو موجودی (اختیاری/پیشنهادی).

## ریسک‌های اصلی
- وابستگی به DNS/TLS خارجی برای دامنه‌های سفارشی و صدور گواهی
- Host header poisoning اگر TrustedHost به‌درستی تنظیم نشود
- نداشتن رزرو موجودی همزمان در checkoutهای هم‌زمان
- گیت‌نشدن یکپارچه در همه سطوح (UI/Policy/API/Jobs)
- نیاز به تنظیم API Key و هدر امضای وبهوک eSIM Go در محیط واقعی
- اجرای تست کامل با محیط فعلی هنوز Fail دارد و نیاز به ریشه‌یابی/seed تکمیلی دارد
- نیاز به اجرای worker صف providers برای پردازش jobs (در حالت queue)
- نیاز به اجرای queue worker برای صف providers در محیط تولید

## 10 کار بعدی (اولویت‌دار)
- تنظیم کلید API و هدر امضای وبهوک eSIM Go در محیط واقعی
- تایید DNS/TLS واقعی برای دامنه‌های سفارشی (در صورت نیاز عملیاتی)
- افزودن تست همزمانی برای رزرو موجودی (در صورت نیاز QA عمیق)
- بررسی Failهای تست سراسری و تکمیل seed/fixture موردنیاز
- راه‌اندازی queue worker برای صف `providers` (در صورت استفاده از queue)
- اطمینان از اجرای queue worker با صف `providers`
