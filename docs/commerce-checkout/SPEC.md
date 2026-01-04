# SPEC — commerce-checkout

## معرفی
- پکیج: haida/commerce-checkout
- توضیح: Commerce cart and checkout flows for Haida platform.
- Service Provider: Haida\CommerceCheckout\CommerceCheckoutServiceProvider
- Filament Plugin: ندارد

## دامنه و قابلیت‌ها
- مدل‌ها:
- Cart.php
- CartItem.php
- منابع Filament:
- ندارد
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/CartController.php
- Api/V1/CartItemController.php
- Api/V1/CheckoutController.php
- Api/V1/OpenApiController.php
- Jobs/Queue:
- ندارد
- Policyها:
- CartItemPolicy.php
- CartPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): commerce.cart.manage, commerce.cart.view, commerce.checkout.create

## مدل داده
- Migrations:
- 2025_12_30_000012_create_commerce_checkout_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/commerce-checkout/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/commerce-checkout/config/commerce-checkout.php
- کلیدهای env مرتبط:
- COMMERCE_CHECKOUT_API_RATE_LIMIT
- COMMERCE_CHECKOUT_DEFAULT_WAREHOUSE_ID
- COMMERCE_CHECKOUT_INVENTORY_ENABLED

## استقرار در پنل‌ها
- Admin Panel: Plugin ندارد
- Tenant Panel: Plugin ندارد
