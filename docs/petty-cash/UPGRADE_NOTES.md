# یادداشت‌های ارتقا (Petty Cash)

## خلاصه
این ارتقا ساختار تمیز (Domain/Application/Infrastructure/UI/API)، کنترل‌های مستمر، گردش‌کار پویا، گزارش‌دهی پیشرفته و لایه هوش مصنوعی ایمن را اضافه می‌کند.

## سازگاری عقب‌رو
- مسیرهای API بدون تغییر باقی مانده‌اند: `/api/v1/petty-cash/*`
- نام مجوزهای قبلی تغییر نکرده‌اند.
- ستون‌های جدید افزوده شده‌اند و حذف/تغییر مخرب نداریم.

## مهاجرت‌ها
```
php artisan migrate --force
```

## تغییرات پیکربندی
افزودن بخش جدید `ai` در `config/filament-petty-cash-ir.php`:
- فعال‌سازی AI: `ai.enabled`
- ارائه‌دهنده: `ai.provider`
- نگهداشت/رداکشن: `ai.allow_store_prompts`, `ai.redaction`, `ai.log_retention_days`
- کنترل ناهنجاری: `ai.anomaly_threshold`, `ai.create_exceptions`, `ai.max_scan`

## مجوزهای جدید
- `petty_cash.workflow.view/manage`
- `petty_cash.controls.reconcile.view/manage`
- `petty_cash.controls.cash_count.view/manage`
- `petty_cash.exceptions.view/manage`
- `petty_cash.expense.reverse`
- `petty_cash.replenishment.reverse`
- `petty_cash.settlement.reverse`
- `petty_cash.ai.use`
- `petty_cash.ai.view_reports`
- `petty_cash.ai.manage_settings`

## قابلیت‌های جدید
- گردش‌کار پویا با قواعد آستانه و تفکیک نقش‌ها.
- کنترل‌های مستمر: شمارش نقدی، تطبیق، و مدیریت استثناها.
- گزارش مدیریتی هوشمند و داشبورد کنترلی.
- پیشنهاد هوشمند روی فرم هزینه و تحلیل ناهنجاری‌ها.

## یادآوری
AI به‌صورت پیش‌فرض خاموش است و تنها با مجوزهای مشخص فعال می‌شود. جزئیات در `docs/petty-cash/AI_GOVERNANCE.md`.
