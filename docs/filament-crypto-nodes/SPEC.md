# SPEC — filament-crypto-nodes

## معرفی
- پکیج: haida/filament-crypto-nodes
- توضیح: Self-hosted crypto node connectors and BTCPay fallback.
- Service Provider: Haida\FilamentCryptoNodes\FilamentCryptoNodesServiceProvider
- Filament Plugin: Haida\FilamentCryptoNodes\FilamentCryptoNodesPlugin (id: filament-crypto-nodes)

## دامنه و قابلیت‌ها
- مدل‌ها:
- CryptoNodeConnector.php
- منابع Filament:
- src/Filament/Resources/CryptoNodeConnectorResource.php
- کنترلرها/API:
- ندارد
- Jobs/Queue:
- ندارد
- Policyها:
- CryptoNodeConnectorPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: [ASSUMPTION] نیازمند بررسی/افزودن
- TenantContext در کد: [ASSUMPTION] نیازمند بررسی
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): ندارد

## مدل داده
- Migrations:
- 2026_01_10_000003_create_crypto_node_connectors_table.php
- جدول‌ها:
- crypto_node_connectors
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: ندارد
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/filament-crypto-nodes/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-crypto-nodes/config/filament-crypto-nodes.php
- کلیدهای env مرتبط:
- BITCOIN_RPC_PASSWORD
- BITCOIN_RPC_URL
- BITCOIN_RPC_USER
- BTCPAY_API_KEY
- BTCPAY_BASE_URL
- BTCPAY_STORE_ID
- BTCPAY_WEBHOOK_SECRET
- EVM_RPC_URL

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
