# SPEC — افزونه 3CX

## هدف
یکپارچه‌سازی 3CX با پلتفرم Filament چند-اجاره‌گی برای مدیریت اتصال‌ها، همگام‌سازی داده‌ها، عملیات Call Control مجاز، و اتصال CRM (ورودی) با رعایت امنیت، مجوزها و تجربه فارسی.

## معماری کلان
- بسته مستقل: `packages/filament-threecx` با ServiceProvider و Filament Plugin (ثبت Resource/Page/Widget فقط از طریق Plugin).
- کلاینت‌ها:
  - `ThreeCxHttp` (پایه برای Http:: با تنظیمات timeout/retry/UA/correlation-id).
  - `ThreeCxAuthService` (مدیریت توکن، cache، refresh و retry-401 فقط یک‌بار).
  - `XapiClient` برای XAPI (OData query + عملیات مهم).
  - `CallControlClient` برای Call Control (عملیات مجاز).
- لایه دامنه:
  - مدل‌های Tenant-scoped با `BelongsToTenant` و جدول‌های با پیشوند `threecx_`.
  - سرویس‌های Sync و CRM Connector.
  - ثبت Audit Log امن و قرمز‌سازی داده‌ها.
- UI:
  - Resourceهای مدیریتی + صفحه «کاوشگر API».
  - ویجت‌های داشبورد برای آمار تماس.
- API:
  - مسیرهای نسخه‌دار `/api/v1/threecx/...` با middleware استاندارد IAM.
  - مسیرهای CRM Connector با احراز هویت امن و نرخ محدودسازی قابل تنظیم.
- مستندسازی:
  - OpenAPI با الگوی موجود در بسته‌های دیگر + اتصال به Filament API Docs Builder.

## مدل داده
تمام جدول‌ها tenant-scoped هستند و از prefix `threecx_` استفاده می‌کنند.
- `threecx_instances`
  - tenant_id, name, base_url, verify_tls, last_health_at, last_error, last_version, last_capabilities_json
  - client_id, client_secret (encrypted)
  - crm_connector_key (encrypted)
  - crm_connector_key_hash (sha256)
  - xapi_enabled, call_control_enabled, crm_connector_enabled
  - route_point_dn, monitored_dns (json)
- `threecx_token_caches` (اختیاری، fallback برای cache)
  - tenant_id, instance_id, scope, access_token (encrypted), expires_at
- `threecx_sync_cursors`
  - tenant_id, instance_id, entity, cursor(json), last_synced_at
- `threecx_call_logs`
  - tenant_id, instance_id, direction, from_number, to_number, started_at, ended_at, duration, status, external_id, raw_payload
  - ایندکس‌های پیشنهادی: tenant_id, instance_id, started_at, direction, status
- `threecx_contacts` (در صورت نبود CRM خارجی)
  - tenant_id, instance_id, name, phones(json), emails(json), external_id, crm_url, raw_payload
  - ایندکس‌ها: tenant_id, instance_id, external_id
- `threecx_api_audit_logs`
  - tenant_id, instance_id, actor_type, actor_id, api_area, method, path, status_code, duration_ms, correlation_id
  - فقط metadata امن، بدون ذخیره secrets یا body کامل

## مدل مجوزها و قابلیت‌ها
ثبت در `CapabilityRegistry` با برچسب‌های فارسی:
- `threecx.view` — مشاهده یکپارچه‌سازی 3CX
- `threecx.manage` — مدیریت اتصال‌ها و تنظیمات 3CX
- `threecx.sync` — همگام‌سازی داده‌ها
- `threecx.api_explorer` — دسترسی به کاوشگر API
- `threecx.crm_connector` — دسترسی به CRM Connector
تمام اکشن‌های UI و API با `IamAuthorization::allows()` و policy کنترل می‌شوند.

## جریان‌های خروجی (Outbound)
1) **Health Check**
   - درخواست ساده به XAPI برای دریافت نسخه/سلامت، به‌روزرسانی `last_health_at/last_version`.
2) **Token Flow**
   - دریافت توکن با client_id/client_secret.
   - ذخیره cache با TTL.
   - در صورت 401، یک‌بار refresh و retry.
3) **Capability Detection**
   - بررسی نسخه/قابلیت‌ها و ذخیره در `last_capabilities_json`.
4) **Sync**
   - تماس‌های اخیر، مخاطبین، و تاریخچه قابل‌دسترس با cursor.
   - upsert بر اساس external_id و ثبت audit برای هر عملیات خارجی.
5) **Call Control**
   - عملیات مجاز (make/transfer/terminate) با مجوز جداگانه و ثبت audit.
   - محدودسازی درخواست‌ها براساس rate limit.

## جریان‌های ورودی CRM Connector (3CX → SaaS)
مسیر پایه: `/api/v1/threecx/crm`
- احراز هویت پیش‌فرض: کلید اتصال اختصاصی هر instance در هدر (مثلاً `X-ThreeCX-Connector-Key`) با قابلیت تغییر در config.
- گزینه جایگزین (قابل تنظیم): استفاده از `ApiKeyAuth` + `ApiAuth` در صورت نیاز سازمان.
- ResolveTenant بر اساس:
  - کلید اتصال (mapping به instance و tenant)
  - یا پارامتر امن instance/tenant در URL (در صورت الزام 3CX)
- Endpointها:
  - `GET /lookup` (phone/email)
  - `GET /search` (query)
  - `POST /contacts`
  - `POST /journal/call`
  - `POST /journal/chat`
- پاسخ‌ها به شکل سازگار با CRM Wizard و قابل نگاشت هستند.

## همگام‌سازی و Idempotency
- Jobها: `SyncContactsJob`، `SyncCallHistoryJob`، `SyncChatHistoryJob` (اختیاری) + فرمان `threecx:sync`.
- هر Sync job از `ThreeCxSyncCursor` استفاده می‌کند (cursor + last_synced_at).
- مدل‌ها بر اساس `external_id` upsert می‌شوند تا تکرار ایجاد نشود.
- عملیات حساس در transaction اجرا می‌شوند.
- Rate limit قابل تنظیم و احترام به backoff برای خطاهای 429.

## کاوشگر API (API Explorer / Generic Client)
- فقط برای کاربران با `threecx.api_explorer` و Feature Flag فعال.
- ارسال درخواست‌ها از سمت سرور با توکن‌های مدیریت‌شده (عدم نمایش token به کاربر).
- Denylist سخت برای مسیرهای مرتبط با `recording`, `audio`, `monitor`, `stream`, `listen`, `whisper`, `barge` و مشابه (قابل تنظیم).
- ثبت کامل audit بدون ذخیره داده حساس.

## امنیت و ثبت لاگ
- همه‌ی secrets با cast `encrypted` ذخیره می‌شوند.
- لاگ‌ها فقط metadata امن دارند؛ bodyها پیش‌فرض redacted هستند.
- Correlation ID برای ردیابی درخواست‌های خارجی ثبت می‌شود.
- سیاست نگهداشت داده‌ها قابل تنظیم است (مثلاً call logs و audit logs) + فرمان purge در صورت فعال‌سازی.

## چند-اجاره‌گی و اشتراک
- همه‌ی مدل‌ها Tenant-scoped هستند و با `BelongsToTenant` محدود می‌شوند.
- تمام مسیرهای API با `ResolveTenant` و scope middleware محافظت می‌شوند.
- قابلیت‌ها از طریق IAM و Feature Flags قابل کنترل هستند (Subscription gating).

## اعتبارسنجی سناریو
- در `scripts/deep_scenario_runner.php` یک سناریوی 3CX اضافه شده که اتصال نمونه، Health و Sync را با پاسخ‌های fake اجرا می‌کند.
- سناریو idempotent است و از شناسه‌های ثابت برای upsert استفاده می‌کند.

## موارد ممنوع (Surveillance Exclusions)
- هیچ قابلیت شنود/مانیتورینگ، دریافت یا پخش ضبط مکالمه، استریم صدا/رسانه یا دریافت فایل‌های ضبط‌شده پیاده‌سازی نمی‌شود.
- هر endpoint مرتبط با ضبط/مانیتور در کلاینت عمومی و API Explorer مسدود می‌گردد.
- هر گونه قابلیت “whisper/barge-in/silent monitoring” صراحتاً غیرقابل پشتیبانی است.

## توسعه‌پذیری
- رابط `ContactDirectoryInterface` برای اتصال به CRM داخلی/خارجی.
- رویدادهای دامنه برای اتصال به سیستم اعلان (TriggerDispatcher).
- OpenAPI داخلی برای مستندسازی و قابلیت cache اختیاری از spec 3CX (در صورت وجود).

## اعلان‌ها (TriggerDispatcher)
رویدادهای دامنه که برای TriggerDispatcher ارسال می‌شوند:
- `threecx.missed_call_detected` — رویداد تماس بی‌پاسخ
- `threecx.new_contact_created` — ایجاد مخاطب جدید از 3CX
- `threecx.health_degraded` — کاهش سلامت اتصال

نمونه نگاشت تریگر:
- مدل: `ThreeCxCallLog`
- رویداد: `threecx.missed_call_detected`
- پنل: `tenant` (قابل تغییر در config)
