# حاکمیت هوش مصنوعی تنخواه

## اصول کلیدی
- اختیاری و خاموش به‌صورت پیش‌فرض (`ai.enabled=false`).
- شفافیت کامل: پیشنهادها در UI نمایش داده می‌شوند و اعمال آن‌ها نیاز به اقدام کاربر دارد.
- دامنه مجاز: فقط تحلیل تراکنش‌ها، کنترل‌ها، و انطباق مالی.
- ممنوعیت‌ها: هیچ‌گونه امتیازدهی/پروفایل‌سازی کارکنان یا پایش پنهان انجام نمی‌شود.

## مجوزهای دسترسی
- `petty_cash.ai.use`: استفاده از پیشنهادها و اجرای تحلیل هوشمند.
- `petty_cash.ai.view_reports`: مشاهده گزارش‌های مدیریتی هوشمند.
- `petty_cash.ai.manage_settings`: مدیریت تنظیمات هوش مصنوعی و اجازه ذخیره پرامپت‌ها.

## داده و حریم خصوصی
- ذخیره پرامپت خام به‌صورت پیش‌فرض غیرفعال است.
- در صورت فعال بودن `ai.allow_store_prompts` و داشتن مجوز `petty_cash.ai.manage_settings`، داده‌ها ذخیره می‌شوند.
- فهرست فیلدهای قرمز‌شونده از طریق `ai.redaction` کنترل می‌شود.
- نگهداشت لاگ‌ها از طریق `ai.log_retention_days` قابل تنظیم است (نیازمند فرآیند پاک‌سازی زمان‌بندی‌شده).

## لاگ و ممیزی
- همه پیشنهادها در جدول `petty_cash_ai_suggestions` ثبت می‌شوند.
- فیلدهای کلیدی: `suggestion_id`, `suggested_payload`, `reasons`, `status`, `decided_by`, `decided_at`.
- ناهنجاری‌ها می‌توانند به `PettyCashControlException` تبدیل شوند (کنترل‌های مستمر).

## رفتار سیستم
- اگر AI غیرفعال باشد یا مجوز لازم وجود نداشته باشد، هیچ فراخوانی به ارائه‌دهنده انجام نمی‌شود.
- پیشنهادها تنها در صورت درخواست کاربر تولید می‌شوند و امکان رد/پذیرش وجود دارد.

## پیکربندی نمونه
```php
'ai' => [
    'enabled' => false,
    'provider' => \Haida\FilamentPettyCashIr\Infrastructure\Ai\FakeAiProvider::class,
    'allow_store_prompts' => false,
    'redaction' => ['description', 'reference', 'payee_name'],
    'log_retention_days' => 30,
    'anomaly_threshold' => 0.7,
    'create_exceptions' => true,
    'max_scan' => 200,
],
```
