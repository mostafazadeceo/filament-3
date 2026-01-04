# SPEC — filament-crypto-core

## معرفی
- پکیج: haida/filament-crypto-core
- توضیح: Crypto core domain and ledger for Hub.
- Service Provider: Haida\FilamentCryptoCore\FilamentCryptoCoreServiceProvider
- Filament Plugin: Haida\FilamentCryptoCore\FilamentCryptoCorePlugin (id: filament-crypto-core)

## دامنه و قابلیت‌ها
- مدل‌ها:
- CryptoAccount.php
- CryptoAddress.php
- CryptoAuditEvent.php
- CryptoAuditLog.php
- CryptoFeePolicy.php
- CryptoLedger.php
- CryptoLedgerEntry.php
- CryptoNetworkFee.php
- CryptoRate.php
- CryptoWallet.php
- منابع Filament:
- src/Filament/Resources/CryptoAccountResource.php
- src/Filament/Resources/CryptoAddressResource.php
- src/Filament/Resources/CryptoAuditLogResource.php
- src/Filament/Resources/CryptoFeePolicyResource.php
- src/Filament/Resources/CryptoLedgerResource.php
- src/Filament/Resources/CryptoNetworkFeeResource.php
- src/Filament/Resources/CryptoRateResource.php
- src/Filament/Resources/CryptoWalletResource.php
- کنترلرها/API:
- ندارد
- Jobs/Queue:
- ندارد
- Policyها:
- CryptoAccountPolicy.php
- CryptoAddressPolicy.php
- CryptoAuditLogPolicy.php
- CryptoFeePolicyPolicy.php
- CryptoLedgerEntryPolicy.php
- CryptoLedgerPolicy.php
- CryptoNetworkFeePolicy.php
- CryptoRatePolicy.php
- CryptoWalletPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): ندارد

## مدل داده
- Migrations:
- 2026_01_02_000010_create_crypto_core_tables.php
- 2026_01_10_000001_create_crypto_core_tables.php
- جدول‌ها:
- crypto_accounts
- crypto_addresses
- crypto_audit_events
- crypto_fee_policies
- crypto_ledger_entries
- crypto_ledgers
- crypto_network_fees
- crypto_rates
- crypto_wallets
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: ندارد
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/filament-crypto-core/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-crypto-core/config/filament-crypto-core.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
