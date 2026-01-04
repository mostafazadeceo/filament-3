# SPEC — commerce-orders

## معرفی
- پکیج: haida/commerce-orders
- توضیح: Commerce orders and payments for Haida platform.
- Service Provider: Haida\CommerceOrders\CommerceOrdersServiceProvider
- Filament Plugin: Haida\CommerceOrders\CommerceOrdersPlugin (id: commerce-orders)

## دامنه و قابلیت‌ها
- مدل‌ها:
- Order.php
- OrderItem.php
- OrderPayment.php
- OrderRefund.php
- OrderReturn.php
- OrderReturnItem.php
- منابع Filament:
- src/Filament/Resources/CommerceOrderRefundResource.php
- src/Filament/Resources/CommerceOrderResource.php
- src/Filament/Resources/CommerceOrderReturnResource.php
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/OpenApiController.php
- Api/V1/OrderController.php
- Jobs/Queue:
- ندارد
- Policyها:
- OrderPolicy.php
- OrderRefundPolicy.php
- OrderReturnPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): commerce.order.view

## مدل داده
- Migrations:
- 2025_12_30_000011_create_commerce_order_tables.php
- 2026_01_02_000014_create_commerce_order_return_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/commerce-orders/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/commerce-orders/config/commerce-orders.php
- کلیدهای env مرتبط:
- COMMERCE_ORDERS_API_RATE_LIMIT

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
