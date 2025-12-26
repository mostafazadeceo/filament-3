# فهرست مجوزها (پیش‌فرض)

این لیست در `CorePermissions` تعریف شده و توسط دستور `php artisan filamat-iam:sync` ساخته می‌شود.

## مدیریت هویت و دسترسی
- `iam.view`
- `iam.manage`
- `organization.view`
- `organization.manage`
- `tenant.view`
- `tenant.manage`
- `user.view`
- `user.manage`
- `user.invite`
- `user.suspend`
- `user.activate`
- `user.impersonate`
- `user.reset_otp`
- `role.view`
- `role.manage`
- `permission.view`
- `permission.manage`
- `group.view`
- `group.manage`
- `permission_template.view`
- `permission_template.manage`
- `permission_override.view`
- `permission_override.manage`

## دسترسی‌های پیشرفته
- `access_request.view`
- `access_request.manage`
- `access_request.approve`
- `access_request.deny`
- `permission_snapshot.view`
- `permission_snapshot.capture`
- `permission_snapshot.diff`
- `delegated_admin.view`
- `delegated_admin.manage`

## کیف پول
- `wallet.view`
- `wallet.manage`
- `wallet.credit`
- `wallet.debit`
- `wallet.hold`
- `wallet.capture`
- `wallet.release`
- `wallet.transfer`
- `wallet.export`
- `wallet_transaction.view`
- `wallet_transaction.export`
- `wallet_hold.view`
- `wallet_hold.manage`

## اشتراک و پلن
- `subscription.view`
- `subscription.manage`
- `subscription.cancel`
- `subscription.renew`
- `subscription_plan.view`
- `subscription_plan.manage`

## اعلان‌ها
- `notification.view`
- `notification.send`
- `notification.manage`

## API و وبهوک
- `api.view`
- `api.manage`
- `api.key.manage`
- `api.docs.view`
- `api.docs.manage`
- `webhook.view`
- `webhook.manage`

## امنیت و ممیزی
- `security.view`
- `security.manage`
- `audit.view`

## تنظیمات
- `settings.manage`

---

# قالب‌های نقش (پیش‌فرض)

- `super_admin`: همه مجوزها
- `tenant_owner`: مدیریت کامل تننت، کیف پول، اشتراک، اعلان و وبهوک
- `tenant_admin`: مدیریت کاربران و دسترسی‌های پایه
- `finance_manager`: عملیات مالی و گزارش‌ها
- `support`: مشاهده کاربران/اعلان‌ها/امنیت

---

# پلن‌های پیش‌فرض

- `basic` (پایه): مشاهده‌ها و استفاده محدود
- `pro` (حرفه‌ای): مدیریت دسترسی + امکانات بیشتر
- `enterprise` (سازمانی): تمام مجوزها

> پلن‌ها از طریق `SubscriptionPlan` قابل تغییر هستند و می‌توانید مجوزهای خاص هر پلن را تنظیم کنید.

---

# تغییر برچسب‌های فارسی

برچسب‌های فارسی مجوزها از این فایل می‌آیند:
```
packages/filamat-iam-suite/src/Support/PermissionLabels.php
```

اگر نام فارسی یک مجوز را خواستید تغییر دهید یا مجوز جدید اضافه کنید، کافی است این فایل را ویرایش کنید.
