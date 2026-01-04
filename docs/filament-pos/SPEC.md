# SPEC — filament-pos

## معرفی
- پکیج: haida/filament-pos
- توضیح: POS core and offline sync for Hub.
- Service Provider: Haida\FilamentPos\FilamentPosServiceProvider
- Filament Plugin: Haida\FilamentPos\FilamentPosPlugin (id: filament-pos)

## دامنه و قابلیت‌ها
- مدل‌ها:
- PosCashMovement.php
- PosCashierSession.php
- PosDevice.php
- PosOutbox.php
- PosRegister.php
- PosSale.php
- PosSaleItem.php
- PosSalePayment.php
- PosStore.php
- PosSyncCursor.php
- منابع Filament:
- src/Filament/Resources/PosCashMovementResource.php
- src/Filament/Resources/PosCashierSessionResource.php
- src/Filament/Resources/PosDeviceResource.php
- src/Filament/Resources/PosRegisterResource.php
- src/Filament/Resources/PosSaleResource.php
- src/Filament/Resources/PosStoreResource.php
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/OpenApiController.php
- Api/V1/OutboxController.php
- Api/V1/PosSaleController.php
- Api/V1/SyncController.php
- Jobs/Queue:
- ندارد
- Policyها:
- PosCashMovementPolicy.php
- PosCashierSessionPolicy.php
- PosDevicePolicy.php
- PosRegisterPolicy.php
- PosSalePolicy.php
- PosStorePolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): pos.use, pos.view

## مدل داده
- Migrations:
- 2026_01_02_000003_create_pos_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/filament-pos/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-pos/config/filament-pos.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
