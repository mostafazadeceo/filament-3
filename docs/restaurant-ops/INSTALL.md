# نصب Restaurant Ops

## پیش‌نیازها
- PHP 8.4+
- Laravel 12+
- Filament v4
- فعال بودن IAM Suite

## نصب پکیج
```bash
composer require haida/filament-restaurant-ops
php artisan migrate
```

## فعال‌سازی در پنل‌ها
در پنل Admin و Tenant پلاگین زیر را اضافه کنید:
`Haida\FilamentRestaurantOps\FilamentRestaurantOpsPlugin::make()`

## راه‌اندازی اولیه
- تعریف تامین‌کنندگان، کالاها و انبارها.
- تنظیم نقاط سفارش و واحدها.
- ایجاد فرمول تولید (Recipe) برای آیتم‌های منو.
- اتصال POS یا ثبت فروش دستی برای مصرف خودکار.

## داده‌های نمونه (Demo)
برای ساخت داده‌های نمونه و اجرای سناریوی POS→مصرف:
```bash
php artisan db:seed --class=Haida\\FilamentRestaurantOps\\Database\\Seeders\\RestaurantOpsDemoSeeder
```

## API و مستندات
- مسیر API: `/api/v1/restaurant-ops`
- OpenAPI: `/api/v1/restaurant-ops/openapi`
- مجوزها از طریق IAM Suite و اسکوپ‌های `restaurant.*` کنترل می‌شوند.
