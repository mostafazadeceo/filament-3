# Release Plan

## اهداف
- انتشار تدریجی Site OS بدون رگرسیون در ERP موجود.
- فعال‌سازی مرحله‌ای قابلیت‌ها با Feature Gates.

## مراحل انتشار
1) **Staging**
   - اجرای مهاجرت‌ها.
   - فعال‌سازی Feature Gates برای tenant تست.
   - بررسی Workflowها (site publish, checkout, webhook, provider sync).
   - بررسی جریان دامنه و TLS (dns verify + request TLS).

2) **Pilot (Tenant محدود)**
   - فعال‌سازی برای ۱–۲ tenant واقعی.
   - مانیتورینگ لاگ‌ها و نرخ خطا.

3) **Production Rollout**
   - فعال‌سازی تدریجی با plan overrides.
   - افزایش پوشش تست‌ها و مانیتورینگ.

## Migration Safety
- تمام جداول جدید بدون تغییر جداول موجود هستند.
- در صورت rollback، با غیرفعال کردن feature gates، قابلیت‌ها خاموش می‌شوند.

## Rollback
- **گیت‌ها:** ابتدا Feature Gates را برای tenant/feature غیرفعال کنید تا مسیرهای عمومی/پنل فوراً بسته شوند.
- **افزونه‌ها:** در صورت نیاز، افزونه را در `tenant_plugins` غیرفعال کنید (بدون حذف داده).
- **مهاجرت‌ها:** فقط مهاجرت‌های افزوده‌شده در PR آخر را rollback کنید (جدول‌های جدید یا ستون‌های nullable).
- **نسخه افزونه:** از `PluginLifecycleManager::rollback()` برای ثبت نسخه و ردیابی استفاده کنید.
- **وبهوک‌ها/پرداخت‌ها:** در صورت رخداد خطا، endpointهای جدید را rate limit یا غیرفعال کنید تا تراکنش جدید تولید نشود.

## Runbook کوتاه
1) غیرفعال‌سازی feature در پلن/override.
2) غیرفعال‌سازی افزونه در `tenant_plugins`.
3) rollback مهاجرت‌های آخرین PR (در صورت نیاز).
4) بررسی لاگ‌ها + تایید توقف مسیرهای عمومی.

## Monitoring
- استفاده از Correlation ID برای ردیابی درخواست‌ها.
- بررسی Audit Logs برای عملیات حساس.
- پایش وضعیت TLS و خطاهای صدور گواهی (ارجاع به `docs/tls.md`).

## QA عملیاتی
- اجرای `scripts/demo-e2e.sh` با SQLite ایمن برای دمو end-to-end.
- اجرای `scripts/deep_scenario_runner.php` با SQLite ایمن برای سناریوهای عمیق.
