# 20260103 — Tenancy & IAM Model

## Context and Problem Statement
پلتفرم SaaS چند‌مستاجری است و باید همزمان کنترل دسترسی دقیق، scopeهای API، و subscription gating داشته باشد.

## Considered Options
- پیاده‌سازی IAM ساده در لایه اپلیکیشن بدون تیم‌بندی
- استفاده از spatie/laravel-permission با teams + لایه‌های TenantContext
- استفاده از IAM خارجی (SSO/IdP) به‌صورت اجباری

## Decision Outcome
IAM Suite داخلی (`filamat-iam-suite`) با spatie teams و middlewareهای `ApiKeyAuth/ApiAuth/ResolveTenant` انتخاب شد. `TenantContext` برای resolution و `BelongsToTenant` برای scoping استفاده می‌شود.

## Consequences
- همه منابع UI/API باید authorization-aware باشند.
- قابلیت‌ها با Capability Registry ثبت و همگام می‌شوند.
- subscription gating در جریان tenant فعال است و باید در سناریوها تست شود.
