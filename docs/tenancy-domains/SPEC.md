# SPEC — tenancy-domains

## معرفی
- پکیج: haida/tenancy-domains
- توضیح: Host resolver and domain verification for tenant sites.
- Service Provider: Haida\TenancyDomains\TenancyDomainsServiceProvider
- Filament Plugin: Haida\TenancyDomains\TenancyDomainsPlugin (id: tenancy-domains)

## دامنه و قابلیت‌ها
- مدل‌ها:
- SiteDomain.php
- منابع Filament:
- src/Filament/Resources/SiteDomainResource.php
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/DomainController.php
- Jobs/Queue:
- IssueCertificate.php
- Policyها:
- SiteDomainPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: [ASSUMPTION] نیازمند بررسی/افزودن
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): site.domain.manage, site.domain.view

## مدل داده
- Migrations:
- 2025_12_30_000004_create_site_domains_table.php
- 2025_12_30_000021_add_tls_fields_to_site_domains_table.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/tenancy-domains/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/tenancy-domains/config/tenancy-domains.php
- کلیدهای env مرتبط:
- PLATFORM_ALLOWED_HOSTS
- PLATFORM_CNAME_TARGET
- PLATFORM_DOMAIN_VERIFICATION_METHOD
- PLATFORM_ROOT_DOMAIN
- TENANCY_DOMAINS_ACME_DIRECTORY
- TENANCY_DOMAINS_ACME_EMAIL
- TENANCY_DOMAINS_RATE_LIMIT
- TENANCY_DOMAINS_TLS_ENABLED
- TENANCY_DOMAINS_TLS_MODE
- TENANCY_DOMAINS_TLS_PROVIDER
- TENANCY_DOMAINS_TLS_RENEW_BEFORE_DAYS
- TENANCY_DOMAINS_TLS_RETRY_BACKOFF
- TENANCY_DOMAINS_TLS_RETRY_MINUTES
- TENANCY_DOMAINS_TLS_RETRY_TRIES
- TENANCY_DOMAINS_VERIFY_RATE_LIMIT

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
