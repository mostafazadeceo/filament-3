# SMS Bulk Module Installation

## 1) Package Path + Require
`composer.json` includes:
- `"haida/filament-sms-bulk": "^1.0"`
- repository path: `packages/filament-sms-bulk`

## 2) Plugin Registration
Plugin is added to:
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Providers/Filament/TenantPanelProvider.php`

## 3) Environment Variables
Use placeholders and replace with real values in deployment:

```env
IPPANEL_EDGE_BASE_URL=https://edge.ippanel.com/v1
IPPANEL_EDGE_TOKEN=__PUT_EDGE_TOKEN_HERE__
IPPANEL_EDGE_DEFAULT_SENDER=__PUT_SENDER_NUMBER_HERE__
IPPANEL_EDGE_TEST_MOBILE=__PUT_TEST_MOBILE_HERE__
```

## 4) Migrations
Run:

```bash
php artisan migrate --force
```

## 5) Queue
Recommended queue worker for production:

```bash
php artisan queue:work --tries=3 --timeout=120
```

## 6) API Docs
OpenAPI endpoint:
- `/api/v1/sms-bulk/openapi`

## 7) Localization
Enabled locales configured in:
- `packages/filament-sms-bulk/config/filament-sms-bulk.php`

Add a new language by:
1. Creating `resources/lang/<locale>/messages.php`
2. Adding locale to `i18n.enabled_locales`
