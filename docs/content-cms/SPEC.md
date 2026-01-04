# SPEC — content-cms

## معرفی
- پکیج: haida/content-cms
- توضیح: CMS pages for Site OS.
- Service Provider: Haida\ContentCms\ContentCmsServiceProvider
- Filament Plugin: Haida\ContentCms\ContentCmsPlugin (id: content-cms)

## دامنه و قابلیت‌ها
- مدل‌ها:
- CmsPage.php
- CmsPageRevision.php
- منابع Filament:
- src/Filament/Resources/CmsPageResource.php
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/OpenApiController.php
- Api/V1/PageController.php
- Web/PageController.php
- Web/SitemapController.php
- Jobs/Queue:
- ندارد
- Policyها:
- CmsPagePolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): cms.page.view

## مدل داده
- Migrations:
- 2025_12_30_000008_create_content_cms_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: , sitemap.xml, v1, {slug}
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/content-cms/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/content-cms/config/content-cms.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
