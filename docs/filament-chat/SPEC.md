# Filament Chat (Rocket.Chat) — SPEC

## هدف
یکپارچه سازی سرویس چت سازمانی (Rocket.Chat) با هاب برای مدیریت اتصال ها، همگام سازی کاربران، و آماده سازی SSO مبتنی بر OIDC. این ماژول هیچ داده ای از پیام ها ذخیره نمی کند و صرفا سطح مدیریتی/پروویژن را پوشش می دهد.

## دامنه
- مدیریت اتصال های چت به صورت tenant-scoped.
- یک Rocket.Chat مشترک برای همه سازمان‌ها (VM واحد).
- برای هر tenant یک Team و یک اتاق پیش‌فرض ساخته می‌شود تا جداسازی سازمانی حفظ شود.
- همگام سازی کاربران هر فضای کاری با Rocket.Chat و عضویت در Team/Room مربوطه.
- ثبت نگاشت User داخلی ↔ Chat User.
- آماده سازی تنظیمات OIDC برای SSO (تنظیم در خود سرور Rocket.Chat انجام می شود).
  - هاب به عنوان OIDC Provider داخلی عمل می کند (Discovery: `/.well-known/openid-configuration`).

## مدل ها
- `ChatConnection`
  - `tenant_id`, `name`, `provider`, `base_url`, `api_user_id`, `api_token`, `status`
  - `oidc_issuer`, `oidc_client_id`, `oidc_client_secret`, `oidc_scopes`
  - `settings` (json)
    - `settings.oidc_redirect_uris` (اختیاری)
    - `settings.team_prefix` / `settings.room_prefix` (اختیاری)
    - `settings.role_map` (اختیاری)
  - `last_tested_at`, `last_sync_at`, `last_error_message`, `last_error_at`
- `ChatUserLink`
  - `tenant_id`, `chat_connection_id`, `user_id`
  - `chat_user_id`, `username`, `status`, `synced_at`
  - `last_error_message`, `last_error_at`, `metadata`

## مجوزها
- `chat.connection.view`
- `chat.connection.manage`
- `chat.user.view`
- `chat.user.manage`
- `chat.sync`

## گردش کار اصلی
1. ایجاد اتصال چت (Base URL + Admin Token)
2. آزمون اتصال
3. همگام سازی کاربران (ایجاد کاربران در Rocket.Chat و ثبت ChatUserLink)
4. استفاده از OIDC در Rocket.Chat برای ورود کاربران

## نکات امنیتی
- تمام مدل ها tenant-scoped هستند.
- توکن های Rocket.Chat با cast `encrypted` ذخیره می شوند.
- هیچ داده محتوایی از چت دریافت یا ذخیره نمی شود (No-Surveillance).

## ملاحظات توسعه ای
- Rocket.Chat را به عنوان Provider پیش فرض داریم؛ معماری برای افزودن Providerهای دیگر آماده است.
- برای اکشن های `users.update` در Rocket.Chat، در صورت فعال بودن 2FA باید PAT با گزینه bypass 2FA ساخته شود.
