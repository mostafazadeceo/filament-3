# TEST_PLAN

## انواع تست‌ها
- Unit: منطق دامنه، سرویس‌ها، helperها.
- Feature: APIها، Policies، و سناریوهای کاربر.
- Integration: وبهوک‌ها، پرداخت‌ها، ارتباط با providers.
- E2E: اجرای `deep_scenario_runner` برای چند tenant.

## تست‌های اجباری
- Tenancy: جلوگیری از دسترسی cross-tenant.
- Authorization: scope/permission برای UI و API.
- Webhooks: صحت امضا، idempotency، و retry.
- Ledger/Payments: تراکنش‌ها و وضعیت‌ها باید atomic باشند.

## مکان تست‌ها
- `tests/` در ریشه پروژه
- `packages/*/tests` برای ماژول‌ها

[ASSUMPTION] برای هر ماژول حساس باید پوشش تست حداقل سناریوهای CRUD + authorization وجود داشته باشد.
