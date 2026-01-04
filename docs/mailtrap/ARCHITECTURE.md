# معماری افزونه Mailtrap

## اهداف
- اتصال ایمن به Mailtrap برای مدیریت Inboxها، پیام‌ها و دامنه‌های ارسال.
- ارائه UI کامل در Filament برای تیم‌های عملیاتی.
- امکان فروش پکیج Mailtrap در فروشگاه و اعمال Entitlement.
- یکپارچه با IAM، Feature Gates، Notifications و API Docs.

## پکیج‌ها
- `packages/mailtrap-core`
  - کلاینت HTTP + سرویس‌ها + مدل‌ها + API داخلی
  - مدیریت Sync Inbox/Message/Domain
  - انتشار Offer به کاتالوگ و اعمال Entitlement
- `packages/filament-mailtrap`
  - UI مدیریتی (اتصال، Inbox، پیام، دامنه، پکیج فروش)
- `packages/filament-notify-mailtrap`
  - کانال ارسال اعلان از طریق Mailtrap Send API

## نقاط یکپارچه‌سازی
- IAM Suite: سیاست‌ها و مجوزها (Permission Prefix) + Tenant Scoping
- Feature Gates: entitlement از طریق `TenantFeatureOverride`
- Commerce: انتشار Offer به Catalog و اتصال به سفارشات
- Notifications: کانال Mailtrap در Filament Notify
- API Docs: `/api/v1/mailtrap/openapi`

## داده‌های حساس
- `api_token` و `send_api_token` به صورت `encrypted` ذخیره می‌شوند.
- لاگ‌های HTTP بدون ذخیره بدنه حساس تنظیم شده‌اند.

## بدون نظارت (No-Surveillance)
- هیچ داده‌ای از رفتار/موقعیت کاربران جمع‌آوری نمی‌شود.

