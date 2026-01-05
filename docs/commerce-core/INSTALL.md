# Commerce Core Installation

## Requirements
- PHP 8.4+
- Laravel 12+
- Filament v4

## Installation
1. Ensure the local package is available in the root composer path repository.
2. Require the package:

```bash
composer require haida/filament-commerce-core
```

3. Run migrations:

```bash
php artisan migrate --force
```

## Filament plugin
Register the plugin in the tenant panel provider if not already wired:

```php
->plugins([\Haida\FilamentCommerceCore\FilamentCommerceCorePlugin::make()])
```

## IAM permissions
Run capability sync to register permissions and Persian labels:

```bash
php artisan filamat-iam:sync-capabilities
```
