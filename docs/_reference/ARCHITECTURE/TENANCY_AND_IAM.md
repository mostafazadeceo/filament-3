# TENANCY_AND_IAM

این سند نحوه اجرای چند‌مستاجری، IAM و کنترل دسترسی را توضیح می‌دهد.

## جریان Tenant Resolution
1) درخواست وارد می‌شود.
2) Middlewareها فعال می‌شوند: `ApiKeyAuth`، `ApiAuth`، `ResolveTenant`.
3) `TenantContext` tenant فعال را تعیین و در context سراسری قرار می‌دهد.
4) مدل‌ها با `BelongsToTenant` به‌صورت پیش‌فرض scope می‌شوند.

## مدل IAM
- موتور اصلی: `filamat-iam-suite`.
- نقش‌ها/مجوزها: `spatie/laravel-permission` با teams (کلید تیم = `tenant_id`).
- Capability Registry: هر ماژول قابلیت‌ها را ثبت و sync می‌کند.
- برچسب فارسی مجوزها: `PermissionLabels`.

## Scopeهای API
- middleware استاندارد: `filamat-iam.scope:<scope>`.
- scopeها به‌صورت granular در routes ماژول‌ها تعریف شده‌اند.

## Subscription Gating
- IAM Suite به‌صورت پیش‌فرض محدودسازی اشتراک را اعمال می‌کند.
- ماژول‌ها باید هنگام دسترسی tenant سناریوهای subscription را رعایت کنند.

## نکات امنیتی/عملیاتی
- هر UI/API باید authorization-aware باشد.
- عملیات cross-tenant تنها با مجوزهای خاص مجاز است.
- برای همگام‌سازی مجوزها: `php artisan filamat-iam:sync --guard=web`.
