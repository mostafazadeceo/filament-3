# Payments Installation

## Installation
```bash
composer require haida/filament-payments
php artisan migrate --force
```

## Filament plugin
```php
->plugins([\Haida\FilamentPayments\FilamentPaymentsPlugin::make()])
```

## Provider configuration
Set providers in `config/filament-payments.php` and add provider connections per tenant.

## IAM permissions
```bash
php artisan filamat-iam:sync-capabilities
```
