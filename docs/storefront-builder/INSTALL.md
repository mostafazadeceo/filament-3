# Storefront Builder Installation

## Installation
```bash
composer require haida/filament-storefront-builder
php artisan migrate --force
```

## Filament plugin
```php
->plugins([\Haida\FilamentStorefrontBuilder\FilamentStorefrontBuilderPlugin::make()])
```

## IAM permissions
```bash
php artisan filamat-iam:sync-capabilities
```
