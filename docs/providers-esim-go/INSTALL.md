# نصب Provider eSIM Go

## پیش‌نیازها
- Laravel 12+ / PHP 8.4+
- Filament v4
- providers-core و commerce-* نصب شده باشد.

## نصب پکیج‌ها (Path)
- `packages/providers-esim-go-core`
- `packages/providers-esim-go-commerce`
- `packages/providers-esim-go-webhooks`
- `packages/filament-providers-esim-go`

## migrate
```bash
php artisan migrate --force
```

## تنظیمات
فایل‌های config:
- `providers-esim-go-core.php`
- `providers-esim-go-webhooks.php`

مقادیر کلیدی:
- `base_url`
- `sandbox_base_url`
- `api_key_header`
- `signature_headers`
- `rate_limit` (10 TPS)
- `inventory.refund_enabled`
- `webhooks.connection_id_param`
- `notifications.panel` (پیش‌فرض tenant)
- `force_site_currency` (اگر نرخ FX موجود نبود، ارز سایت را اعمال می‌کند)

## پنل
- Admin + Tenant panel: افزونه `FilamentProvidersEsimGoPlugin` فعال است.

## وبهوک
نشانی پیش‌فرض:
```
/api/v1/providers/esim-go/callback?connection_id=<id>
```
