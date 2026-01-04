# 20260103 — Data Model & Indexing Strategy

## Context and Problem Statement
ماژول‌ها تعداد زیادی جدول و کوئری‌های پرتکرار (tenant_id، status، updated_at و ...) دارند. نبود ایندکس‌های هدفمند باعث افت عملکرد می‌شود.

## Considered Options
- تکیه صرف بر کلیدهای اصلی و FKها
- افزودن ایندکس‌های هدفمند در migrations برای فیلترهای پرتکرار
- ساخت ایندکس‌ها به‌صورت دستی در production

## Decision Outcome
ایندکس‌های هدفمند در migrations ماژول‌ها تعریف می‌شوند (به‌ویژه tenant_id، وضعیت‌ها، زمان‌ها و کلیدهای خارجی). این تصمیم در MIGRATION_GUIDE و DATA_MODEL مستند می‌شود.

## Consequences
- مهاجرت‌ها باید با دقت ordering و performance اجرا شوند.
- تغییر ایندکس نیاز به برنامه rollback و اطلاع‌رسانی دارد.
