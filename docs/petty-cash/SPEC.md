# Petty Cash SPEC (تنخواه‌گردان)

## اهداف محصول
- مدیریت دقیق تنخواه شعب رستوران با ثبت کامل هزینه‌های خرد و اسناد.
- کنترل سقف‌ها، وضعیت‌ها و گردش‌کار تأیید برای جلوگیری از تخلفات.
- اتصال به حسابداری برای ثبت اسناد هزینه و تغذیه تنخواه.
- API کامل برای یکپارچگی با سایر ماژول‌ها و بات‌ها.

## غیرهدف‌ها
- جایگزینی کامل خزانه‌داری یا بانکداری.
- ویژگی‌های نظارتی/پایش رفتار پرسنل.

## مدل دامنه (MVP)
- PettyCashFund (صندوق تنخواه)
- PettyCashCategory (دسته هزینه)
- PettyCashExpense (هزینه تنخواه)
- PettyCashExpenseAttachment (پیوست هزینه)
- PettyCashReplenishment (تغذیه تنخواه)
- PettyCashSettlement + PettyCashSettlementItem (تسویه دوره‌ای)
- PettyCashAuditEvent (ردپای رویداد)

## جریان‌های اصلی
- هزینه: پیش‌نویس → ارسال → تأیید → پرداخت → تسویه
- تغذیه: پیش‌نویس → ارسال → تأیید → پرداخت
- تسویه: پیش‌نویس → ارسال → تأیید → قطعی‌سازی + علامت‌گذاری هزینه‌های تسویه‌شده

## معماری داده و ایندکس‌ها
- تمام جداول دارای `tenant_id`, `company_id` و در صورت نیاز `branch_id`.
- ایندکس‌های کلیدی: `tenant_id`, `company_id`, `fund_id`, `status`, `expense_date`, `request_date`.
- Soft delete برای داده‌های مرجع (صندوق‌ها و دسته‌ها).

## اتصال به حسابداری
- `fund.accounting_cash_account_id` برای ثبت هزینه‌ها (بستانکار تنخواه).
- `fund.default_expense_account_id` یا `category.accounting_account_id` برای ثبت بدهکار هزینه.
- `fund.accounting_source_account_id` برای تغذیه تنخواه (بستانکار منبع).
- در صورت تکمیل حساب‌ها، ثبت خودکار `JournalEntry` انجام می‌شود.

## امنیت و نقش‌ها
- مجوزهای ریز برای صندوق، دسته، هزینه، تغذیه، تسویه، و گزارش.
- تفکیک وظایف: ثبت ≠ تأیید ≠ پرداخت ≠ قطعی‌سازی.
- ثبت رویدادهای حساس در `PettyCashAuditEvent`.

## نقشه UI (Filament v4)
- تنخواه‌ها
- دسته‌های هزینه
- هزینه‌های تنخواه (با پیوست‌ها)
- تغذیه تنخواه
- تسویه‌ها

## API v1
- `/api/v1/petty-cash/funds`
- `/api/v1/petty-cash/categories`
- `/api/v1/petty-cash/expenses` (+ submit/approve/reject/post)
- `/api/v1/petty-cash/replenishments` (+ submit/approve/reject/post)
- `/api/v1/petty-cash/settlements` (+ submit/approve/post)
- `/api/v1/petty-cash/openapi`
