# POS Installation

## Installation
```bash
composer require haida/filament-pos
php artisan migrate --force
```

## Filament plugin
```php
->plugins([\Haida\FilamentPos\FilamentPosPlugin::make()])
```

## IAM permissions
```bash
php artisan filamat-iam:sync-capabilities
```
