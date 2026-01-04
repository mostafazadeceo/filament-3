# Marketplace Connectors Installation

## Installation
```bash
composer require haida/filament-marketplace-connectors
php artisan migrate --force
```

## Filament plugin
```php
->plugins([\Haida\FilamentMarketplaceConnectors\FilamentMarketplaceConnectorsPlugin::make()])
```

## IAM permissions
```bash
php artisan filamat-iam:sync-capabilities
```
