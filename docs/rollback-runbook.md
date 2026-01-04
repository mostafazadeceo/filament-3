# Runbook بازگشت (Rollback)

## هدف
بازگردانی سریع بدون حذف داده و با حداقل توقف سرویس.

## مراحل سریع
1) Feature Gate مربوطه را برای tenant/plan غیرفعال کنید.
2) در جدول `tenant_plugins` افزونه را `enabled=false` کنید.
3) در صورت نیاز فقط مهاجرت‌های PR آخر را rollback کنید.
4) endpointهای جدید را rate limit یا موقتاً غیرفعال کنید.
5) لاگ‌ها را با Correlation ID بررسی و تایید کنید که مسیرها بسته‌اند.

## نکات
- داده‌ها حذف نمی‌شوند؛ disable صرفاً gate است.
- rollback فقط روی schema افزوده شده (nullable/جدول جدید) انجام شود.
