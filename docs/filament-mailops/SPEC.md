# Filament MailOps — SPEC

## هدف
مدیریت کامل ایمیل‌های هر فضای کاری (دامنه، صندوق، نام مستعار، ارسال و دریافت) با رعایت چندمستاجری و IAM.

## دامنه‌ی قابلیت‌ها
- مدیریت دامنه‌های ایمیل و نگهداری DNS snapshot.
- ممیزی خودکار DNS برای دامنه‌ها با امتیاز سلامت (`dns_health_score`) و وضعیت (`dns_health_status`).
- مدیریت صندوق‌های ایمیل (ایجاد/ویرایش) و تنظیمات پیشرفته (IMAP/POP3/Forward/Auto‑reply).
- مدیریت نام‌های مستعار (aliases).
- ارسال ایمیل خروجی از طریق SMTP با لاگ وضعیت.
- همگام‌سازی ایمیل‌های ورودی از طریق IMAP و ذخیره رکورد.
- قابلیت غیرفعال‌سازی ذخیره‌ی متن ایمیل‌ها از طریق تنظیمات.
- امکان تنظیم SMTP/IMAP به‌صورت سراسری یا اختصاصی برای هر صندوق.
- همگام‌سازی اختیاری با Mailu API برای ساخت Domain/User/Alias.

## مدل داده
- `mailops_domains`: دامنه‌ها، وضعیت، DKIM selector/public key، DNS snapshot، وضعیت سلامت DNS، لیست خطاهای DNS.
- `mailops_mailboxes`: صندوق‌ها، آدرس کامل، رمز عبور (encrypted)، تنظیمات پیشرفته.
- `mailops_aliases`: نام مستعار و مقصدها.
- `mailops_outbound_messages`: پیام‌های ارسالی و وضعیت ارسال.
- `mailops_inbound_messages`: پیام‌های دریافتی و متادیتا.

## چندمستاجری و IAM
- همه‌ی مدل‌ها `BelongsToTenant` و دارای `tenant_id` هستند.
- منابع Filament از `IamResource` و `InteractsWithTenant` استفاده می‌کنند.
- مجوزها در `MailOpsCapabilities` ثبت می‌شوند.

## مجوزها
- `mailops.domain.view|manage`
- `mailops.mailbox.view|manage`
- `mailops.alias.view|manage`
- `mailops.outbound.view|send`
- `mailops.inbound.view|sync`
- `mailops.settings.manage`

## همگام‌سازی با Mailu
- فعال‌سازی از طریق `MAILOPS_MAILU_ENABLED=true`.
- نیازمند `MAILOPS_MAILU_BASE_URL` و `MAILOPS_MAILU_TOKEN`.
- عملیات‌ها: ایجاد/به‌روزرسانی دامنه، صندوق، نام مستعار.
- برای دامنه‌ها، snapshot رکوردهای DNS گرفته می‌شود و ممیزی سلامت DNS انجام می‌شود.

## UX و عملیات دامنه
- فرم دامنه به سه بخش تقسیم شده: اطلاعات دامنه، امنیت DNS، وضعیت همگام‌سازی.
- اکشن‌های عملیاتی دامنه:
  - `همگام‌سازی Mailu`
  - `به‌روزرسانی Snapshot DNS`
  - `ممیزی DNS`
  - `راهنمای رکورد DNS (Copy/Paste)`
- جدول دامنه‌ها دارای فیلترهای وضعیت، وضعیت Sync و سلامت DNS است.

## ارسال/دریافت
- ارسال از طریق SMTP با اطلاعات صندوق.
- دریافت از IMAP (نیازمند افزونه PHP `imap`).

## محدودیت‌ها
- دریافت ایمیل فقط در صورت وجود افزونه `imap` فعال است.
- ارسال/دریافت وابسته به اعتبارسنجی SMTP/IMAP سرور است.
