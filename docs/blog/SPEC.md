# SPEC — blog

## معرفی
- پکیج: haida/blog
- توضیح: Blog module for Site OS.
- Service Provider: Haida\Blog\BlogServiceProvider
- Filament Plugin: Haida\Blog\BlogPlugin (id: blog)

## دامنه و قابلیت‌ها
- مدل‌ها:
- BlogCategory.php
- BlogPost.php
- BlogTag.php
- منابع Filament:
- src/Filament/Resources/BlogCategoryResource.php
- src/Filament/Resources/BlogPostResource.php
- src/Filament/Resources/BlogTagResource.php
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/BlogCategoryController.php
- Api/V1/BlogPostController.php
- Api/V1/BlogTagController.php
- Api/V1/OpenApiController.php
- Web/BlogController.php
- Jobs/Queue:
- ندارد
- Policyها:
- BlogCategoryPolicy.php
- BlogPostPolicy.php
- BlogTagPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): blog.post.view

## مدل داده
- Migrations:
- 2025_12_30_000009_create_blog_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/blog/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/blog/config/blog.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
