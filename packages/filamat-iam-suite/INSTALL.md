# راهنمای نصب

## 1) نصب بسته
```
composer require filamat/filamat-iam-suite
```

## 2) انتشار کانفیگ و مهاجرت‌ها
```
php artisan filamat-iam:install --force --seed
```

## 3) تنظیم مدل کاربر
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

## 4) فعال‌سازی Teams در Spatie Permission
```
// config/permission.php
'teams' => true,
'team_foreign_key' => 'tenant_id',
```

## 5) ثبت پلاگین در پنل‌ها
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

## 6) تنظیمات مبدل اعلان/رمز یکبارمصرف
```
// config/filamat-iam.php
'notification_adapter' => 'custom_plugin',
```
رویدادهای `OtpRequested` و `NotificationRequested` را در پروژه خود هندل کنید.

## 7) تنظیم اسکوپ‌های API
- توکن‌های Sanctum می‌توانند با `abilities` ساخته شوند (مثال: `iam.view`, `wallet.manage`).
- برای اسکوپ تننت می‌توانید از توانایی `tenant:{id}` یا هدر `X-Tenant-ID` استفاده کنید.
- برای API Key، فیلد `abilities` تعیین‌کننده‌ی دسترسی‌هاست (در صورت نیاز از `*` برای دسترسی کامل).

## 8) همگام‌سازی مجوزها
```
php artisan filamat-iam:sync
```

## 9) اجرای صف و زمان‌بند
```
php artisan queue:work
php artisan schedule:work
```

## 10) امپرسونیشن
- مسیر توقف: `/filamat-iam/impersonation/stop`
- فقط سوپرادمین‌ها می‌توانند امپرسونیشن را آغاز کنند (در پنل کلان).

## 11) نمونه Seeder
Seeder پیش‌فرض در مسیر زیر است:
```
Filamat\IamSuite\Database\Seeders\FilamatIamSeeder
```

برای اجرا:
```
php artisan db:seed --class=Filamat\\IamSuite\\Database\\Seeders\\FilamatIamSeeder
```

## 12) مستندات API (Filament API Docs Builder)
راهنمای کامل در این مسیر قرار دارد:
```
packages/filamat-iam-suite/docs/api-docs-builder.md
```

برای کنترل دسترسی مستندات:
- `api.docs.view`
- `api.docs.manage`

در صورت نبود این مجوزها، سیستم از `api.view` و `api.manage` استفاده می‌کند.

## 13) فهرست مجوزها
لیست کامل مجوزهای پیش‌فرض و نقش‌ها:
```
packages/filamat-iam-suite/docs/permissions.md
```
