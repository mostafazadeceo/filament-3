# راهنمای Offline Sync

## اصول
- Local-first، server-authoritative، conflict-aware.
- Outbox برای عملیات write (create/update/delete).
- Pull بر پایه cursor (timestamp).
- Retry با exponential backoff.

## معماری Sync
### Web
- IndexedDB (Dexie) برای outbox و کش.
- Background Sync اگر در دسترس باشد، fallback: sync هنگام باز شدن اپ.

### Android
- Room برای outbox و کش.
- WorkManager برای sync با محدودیت شبکه و backoff نمایی.

## سیاست‌های تعارض (اجباری)
- POS Order: پس از finalize immutable؛ قبل از finalize merge با LWW + audit.
- Chat/Ticket: append-only.
- Attendance: immutable + correction workflow.
- Tasks: LWW + audit trail.

## جریان Push/Pull
1) عملیات در Outbox ثبت می‌شود.
2) هنگام آنلاین شدن، batch به `/api/v1/app/sync/push` ارسال می‌شود.
3) سرور پاسخ می‌دهد: `accepted | conflict | failed`.
4) Pull تغییرات با `/api/v1/app/sync/pull?cursor=...`.

## Idempotency
- هر عملیات حساس با `client_request_id`/`idempotency_key` ارسال می‌شود.
- سرور عملیات تکراری را امن و بدون تکرار اثر می‌پذیرد.

## Observability
- متریک‌ها: `sync_success`, `sync_fail`, `queue_size`, `latency_ms`.
- ثبت خطاها بدون اطلاعات حساس.

## محدودیت‌های فعلی
- Sync v1 فقط تغییرات ثبت‌شده در `app_sync_changes` را برمی‌گرداند.
- اتصال عمیق به ماژول‌های POS/HR/Workhub در فاز بعدی تکمیل می‌شود.
