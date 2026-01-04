# SPEC — filament-currency-rates

## معرفی
- پکیج: haida/filament-currency-rates
- توضیح: افزونه فیلامنت برای همگام‌سازی و ارائه نرخ ارز به ریال ایران.
- Service Provider: Haida\FilamentCurrencyRates\CurrencyRatesServiceProvider
- Filament Plugin: Haida\FilamentCurrencyRates\CurrencyRatesPlugin (id: currency-rates)

## دامنه و قابلیت‌ها
- مدل‌ها:
- CurrencyRate.php
- CurrencyRateRun.php
- منابع Filament:
- src/Resources/CurrencyRateResource.php
- src/Resources/CurrencyRateRunResource.php
- کنترلرها/API:
- CurrencyRateApiController.php
- Jobs/Queue:
- AutoSyncCurrencyRatesJob.php
- SyncCurrencyRatesJob.php
- Policyها:
- ندارد

## Tenancy و IAM
- BelongsToTenant در کد: [ASSUMPTION] نیازمند بررسی/افزودن
- TenantContext در کد: [ASSUMPTION] نیازمند بررسی
- IamAuthorization::allows در کد: [ASSUMPTION] نیازمند بررسی
- Capability Registry: [ASSUMPTION] در صورت وجود باید ثبت شود
- Scopeها (API): ندارد

## مدل داده
- Migrations:
- 2025_01_01_000001_create_currency_rates_table.php
- 2025_01_01_000002_create_currency_rate_runs_table.php
- جدول‌ها:
- currency_rate_runs
- currency_rates
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: , currency-rates
- OpenAPI: ندارد/نامشخص
- جزئیات: `docs/filament-currency-rates/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-currency-rates/config/currency-rates.php
- کلیدهای env مرتبط:
- CURRENCY_RATES_API_TOKEN
- CURRENCY_RATES_API_URL
- CURRENCY_RATES_PUBLIC_TOKEN

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت نشده در TenantPanelProvider
