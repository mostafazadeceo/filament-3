# گزارش اکتشاف مخزن — Meetings AI

## خلاصه
- پکیج Meetings در مخزن موجود نیست؛ باید به‌صورت پکیج جدید ساخته شود.
- الگوی استاندارد پکیج‌ها: `packages/<module>` با `spatie/laravel-package-tools` و Plugin Filament.
- IAM Suite و تننت‌محوری به‌عنوان مرجع امنیتی/چندتننتی باید استفاده شود.
- اعلان‌ها و وبهوک‌ها در مخزن موجود هستند و باید برای جلسات هم استفاده شوند.

## الگوهای کلیدی برای پیاده‌سازی Meetings
- IAM Suite:
  - trait `BelongsToTenant` برای اسکوپ تننت.
  - `TenantContext` برای گرفتن تننت جاری.
  - `IamAuthorization::allows()` برای کنترل دسترسی.
  - Capability Registry برای ثبت مجوزهای جدید.
- API conventions:
  - مسیر پایه `/api/v1/<module>`.
  - middlewareها: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, `filamat-iam.scope:<scope>`.
- Filament:
  - استفاده از `IamResource` برای map شدن مجوزها.
  - ثبت Resources/Pages/Widgets از طریق Plugin.

## اعلان‌ها و وبهوک
- اعلان‌ها از `haida/filament-notify-core` استفاده می‌کنند (RuleEngine + TriggerDispatcher).
- وبهوک‌ها در `filamat-iam-suite` پیاده شده‌اند (HMAC + nonce + idempotency).
- الگوی رویداد برای n8n و اتوماسیون در `n8n_event_catalog.php` تعریف می‌شود.

## نقاط ادغام با Workhub
- Workhub دارای EntityReferenceRegistry برای لینک‌دهی بین ماژول‌هاست.
- Workhub از WebhookService (type=workhub) و WorkhubEventSubscriber برای ارسال رویداد استفاده می‌کند.

## شکاف‌ها (برای پر کردن)
- پکیج Meetings باید از صفر ساخته شود (مدل‌ها، منابع، API، وبهوک‌ها).
- AI core باید پکیج مشترک باشد (providers + consent + audit).
- OpenAPI باید با `filament-api-docs-builder` منتشر شود.
