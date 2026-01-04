# Commerce Experience Installation

## Installation
```bash
composer require haida/filament-commerce-experience
php artisan migrate --force
```

## Filament plugin
```php
->plugins([\Haida\FilamentCommerceExperience\FilamentCommerceExperiencePlugin::make()])
```

## IAM permissions
```bash
php artisan filamat-iam:sync-capabilities
```
