# گزارش اکتشاف مخزن — Workhub AI

## خلاصه
- پکیج Workhub در `packages/filament-workhub` موجود است و به‌صورت کامل از Filament v4 و IAM Suite استفاده می‌کند.
- الگوی تننت و دسترسی، مبتنی بر `Filamat\IamSuite` است (TenantContext/BelongsToTenant/IamAuthorization + middlewareهای API).
- وبهوک‌ها و اتوماسیون بر بستر `filamat-iam-suite` پیاده شده‌اند؛ Workhub از Dispatcher اختصاصی خودش استفاده می‌کند.
- سیستم اعلان‌ها بر پایه `haida/filament-notify-core` و درایورهای کانال مختلف است.

## Workhub (پکیج موجود)
- مسیر پکیج: `packages/filament-workhub`.
- سرویس‌پراوایدر: `Haida\FilamentWorkhub\FilamentWorkhubServiceProvider`.
  - مهاجرت‌ها با پیشوند `workhub_` ثبت شده‌اند.
  - رویدادها با `WorkhubEventSubscriber` منتشر و به وبهوک/اتوماسیون ارسال می‌شوند.
- پلاگین Filament: `Haida\FilamentWorkhub\FilamentWorkhubPlugin` (ثبت Resources/Pages).
- مدل‌ها: `Project`, `WorkItem`, `Workflow`, `Status`, `Transition`, `Comment`, `Attachment`, `Watcher`, `Decision`, `CustomField`, `AutomationRule`.
- الگوی تننت: `UsesTenant` (داخلی Workhub) از `BelongsToTenant` استفاده می‌کند و tenant_id را خودکار ست می‌کند.
- منابع Filament اکثراً از `Filamat\IamSuite\Filament\Resources\IamResource` استفاده می‌کنند و `permissionPrefix` دارند.

## IAM و چندتننتی
- هسته IAM در `packages/filamat-iam-suite`.
- اسکوپ تننت با `TenantContext` + `TenantScope` و trait `BelongsToTenant` اعمال می‌شود.
- الگوی مجوز در UI/Policy: `IamAuthorization::allows()`.
- مجوزها در Capability Registry ثبت می‌شوند (`CapabilityRegistryInterface`).
- API middlewareها: `ApiKeyAuth`, `ApiAuth`, `ResolveTenant`, `filamat-iam.scope:*`.

## اعلان‌ها (Notifications)
- پکیج پایه: `packages/filament-notify-core`.
- مکانیزم Trigger: `TriggerDispatcher` با `RuleEngine` و `TriggerService`.
- درایورها/کانال‌ها: telegram/whatsapp/bale/sms/webpush/mailtrap و سایر پکیج‌ها زیر `packages/filament-notify-*`.

## وبهوک و اتوماسیون
- مدل‌ها و جداول: `webhooks`, `webhook_deliveries`, `webhook_nonces` در `filamat-iam-suite`.
- امضا و امنیت وبهوک: HMAC + timestamp + nonce + replay protection در `WebhookService`.
- ساخت envelope رویدادها: `IamEventEnvelopeFactory` با redaction policy.
- کاتالوگ رویداد n8n: `packages/filamat-iam-suite/config/n8n_event_catalog.php`.
- Workhub Dispatcher: `Haida\FilamentWorkhub\Services\WorkhubWebhookDispatcher` با type=`workhub`.

## API Docs
- در IAM Suite، `zpmlabs/filament-api-docs-builder` نصب است و Resource مرتبط در IAM وجود دارد.
- Workhub فعلی OpenAPI استاتیک دارد (`WorkhubOpenApi`).

## جمع‌بندی نیازهای افزونه AI
- پکیج جدید AI باید با الگوی IAM Suite (TenantContext/IamAuthorization/CapabilityRegistry) و وبهوک‌های موجود سازگار شود.
- رویدادهای AI Workhub باید به سیستم وبهوک موجود (type=workhub) متصل شوند.
- ادغام اعلان‌ها باید از `filament-notify-core` استفاده کند.
- پکیج AI باید در معماری پکیج‌ها (`packages/*`) ساخته شود.
