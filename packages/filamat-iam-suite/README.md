# Filamat IAM Suite (Filament v4)

افزونه جامع مدیریت هویت/دسترسی/کیف‌پول/اشتراک برای Filament v4 با پشتیبانی چندتننتی، پنل سوپرادمین و ای‌پی‌آی نسخه‌بندی‌شده.

## نیازمندی‌ها
- PHP 8.2+
- Laravel 11.28+
- Filament v4
- Sanctum
- Spatie Permission

## نصب
### 1) افزودن بسته (محلی)
```
composer require filamat/filamat-iam-suite
```

### 2) انتشار منابع و مهاجرت‌ها
```
php artisan filamat-iam:install --force --seed
```

### 3) افزودن Trait به مدل کاربر
```php
use Laravel\Sanctum\HasApiTokens;
use Filamat\IamSuite\Support\HasIamSuite;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasIamSuite;
    use HasRoles;
}
```

### 4) فعال‌سازی چندتننتی در Spatie Permission
در `config/permission.php`:
```
'teams' => true,
'team_foreign_key' => 'tenant_id',
```

### 5) ثبت پلاگین در PanelProvider
```php
use Filamat\IamSuite\FilamatIamSuitePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            FilamatIamSuitePlugin::make()
                ->superAdminPanels(['admin'])
                ->tenantPanels(['tenant']),
        ]);
}
```

## اتصال به پلاگین اعلان/رمز یکبارمصرف
در `config/filamat-iam.php` مقدار `notification_adapter` را روی `custom_plugin` بگذارید و رویدادها را مصرف کنید:
```php
Event::listen(\Filamat\IamSuite\Events\OtpRequested::class, function ($event) {
    // ارسال رمز یکبارمصرف از طریق پلاگین شما
});

Event::listen(\Filamat\IamSuite\Events\NotificationRequested::class, function ($event) {
    // ارسال اعلان
});
```

## ثبت قابلیت‌ها توسط ماژول‌ها
```php
app(\Filamat\IamSuite\Contracts\CapabilityRegistryInterface::class)
    ->register(
        module: 'orders',
        permissions: ['orders.view', 'orders.create', 'orders.cancel'],
        featureFlags: ['orders_enabled'],
        quotas: ['orders_per_day' => 100]
    );

php artisan filamat-iam:sync
```

## امپرسونیشن
- توقف امپرسونیشن: `/filamat-iam/impersonation/stop`
- فقط سوپرادمین‌ها در پنل کلان اجازه آغاز دارند.

## اسکوپ‌های API
- توکن Sanctum با `abilities` (مثل `wallet.manage`, `subscription.view`).
- امکان اسکوپ تننت با `tenant:{id}` یا هدر `X-Tenant-ID`.

## ای‌پی‌آی
- مسیرها در `/api/v1/*`
- احراز هویت: توکن Sanctum یا `X-Api-Key`
- فایل OpenAPI: `packages/filamat-iam-suite/docs/openapi.yaml`

## صف و زمان‌بند
```
php artisan queue:work
php artisan schedule:work
```

## داشبوردها و مرکز اعلان‌ها
- داشبورد سوپرادمین، داشبورد تننت و داشبورد اعلان‌ها در پنل‌ها فعال هستند.
- آمارها شامل امنیت، وبهوک، وضعیت صف و نرخ موفقیت OTP است.

## تست
```
cd packages/filamat-iam-suite
./vendor/bin/pest
```
