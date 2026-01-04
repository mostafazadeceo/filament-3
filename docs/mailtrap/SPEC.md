# SPEC — افزونه Mailtrap

## هدف
یکپارچه‌سازی Mailtrap برای مدیریت Inboxها، پیام‌ها و دامنه‌های ارسال + فروش پکیج Mailtrap در فروشگاه.

## دامنه‌ها
- اتصال (Connection): نگهداری توکن API و اطلاعات اکانت.
- Inbox: دریافت و مشاهده پیام‌های تست.
- Message: بدنه HTML/Text + ضمیمه‌ها.
- Sending Domain: وضعیت DNS و آماده‌سازی ارسال واقعی.
- Offer: پکیج فروش Mailtrap برای فعال‌سازی Feature Gate.

## حالت‌ها
- Connection.status: `active | inactive`
- Offer.status: `active | inactive`

## قوانین
- تمام داده‌ها tenant-scoped هستند.
- توکن‌ها encrypted ذخیره می‌شوند.
- Sync با محدودیت نرخ (10TPS) و حداقل فاصله زمانی انجام می‌شود.
- لاگ HTTP بدون ذخیره داده حساس.

## ارتباط با فروشگاه
- هر Offer می‌تواند به یک CatalogProduct منتشر شود.
- پس از پرداخت سفارش، `TenantFeatureOverride` ساخته می‌شود.

## Feature Keys پیشنهادی
- `mailtrap.connection.view`
- `mailtrap.connection.manage`
- `mailtrap.inbox.view`
- `mailtrap.inbox.sync`
- `mailtrap.message.view`
- `mailtrap.domain.view`
- `mailtrap.domain.sync`
- `mailtrap.offer.view`
- `mailtrap.offer.manage`
- `mailtrap.send.test`

## محدودیت‌ها
- ارسال ایمیل از طریق کانال Mailtrap فقط با توکن Send API انجام می‌شود.
- اگر نیاز به چند کانال مستقل باشد، باید در تنظیمات کانال‌های اعلان تعریف شود.

