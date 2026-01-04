# Filamat IAM Suite — N8N Intelligence Connector (n&n)

## Goals
- Production-grade, multi-tenant safe automation connector for IAM/security/access events.
- Reuse existing webhook backbone (Webhook/WebhookDelivery/DeliverWebhookJob).
- Outbound event delivery to n8n, with optional inbound callback for AI reports and proposals.
- Persian-first UX for all UI labels and messages.
- Strict authorization on UI and API, scoped per tenant.

## Non-Goals
- No auto-execution of sensitive actions without explicit approval.
- No invasive surveillance or hidden monitoring features.
- No replacement of existing webhook system; this is an extension.

## Architecture Map (Repo Deep Scan)
- Webhook backbone (IAM suite):
  - `WebhookService` signs and dispatches deliveries via `DeliverWebhookJob`.
  - Models: `Webhook`, `WebhookDelivery`, `WebhookNonce` (HMAC + nonce replay protection).
  - UI: `WebhookResource` and `WebhookDeliveriesRelationManager`.
  - Inbound controllers: `NotificationWebhookController`, `PaymentWebhookController`.
- IAM audit/security sources:
  - `AuditService` logs audit records and is fed by `AuditableObserver` on IAM models (roles, permissions, access requests, api keys, etc).
  - `SecurityEventService` records authentication/OTP/security events.
  - `ImpersonationService` logs audit + security events on start/stop.
  - `AccessRequestService` logs audit + security events on create/approve/deny.
  - `ApiKeyService` creates/rotates API keys (audited via model observer).
  - `SubscriptionService` dispatches `SubscriptionChanged` event.
- Event dispatch patterns from other modules:
  - `WorkhubWebhookDispatcher` and `AccountingWebhookDispatcher` query webhooks by `type` + tenant, filter by events, then `WebhookService::queue`.

### Proposed Connector Components
- `IamWebhookDispatcher` (new): dispatches `IamEvent` to webhooks of type `automation` (n8n).
- Event envelope factory + redaction pipeline (PII minimization, tenant-aware).
- Inbound callback controller `/api/v1/iam/n8n/callback` with ApiKey + ResolveTenant + scope.
- AI report + action proposal models/resources for review and approval queue.

## Envelope Standard (Outbound)
All outgoing payloads to n8n follow this structure:

```json
{
  "event": "iam.user.created",
  "tenant_id": 123,
  "occurred_at": "2026-01-02T12:34:56Z",
  "idempotency_key": "uuid",
  "actor": { "user_id": 1, "type": "user", "ip": "x", "ua": "y" },
  "subject": { "type": "user", "id": 42 },
  "context": { "source": "ui", "trace_id": "..." },
  "data": { "changes": { "name": { "from": "A", "to": "B" } } },
  "links": { "filament_url": "https://app.example/panel/..." }
}
```

- `actor/ip/ua` can be removed or masked per-tenant redaction policy.
- `data` must be minimal and scoped to the tenant.

## Event Catalog
The full catalog is defined in `packages/filamat-iam-suite/config/n8n_event_catalog.php` and referenced by UI dropdowns and validation.

### Event Catalog Table (Core)
| Event Key | Label (FA) | Domain | Default | Sensitivity | Source |
| --- | --- | --- | --- | --- | --- |
| iam.user.created | ایجاد کاربر | iam | on | medium | User observer |
| iam.user.updated | ویرایش کاربر | iam | on | medium | User observer |
| iam.user.deleted | حذف کاربر | iam | on | medium | User observer |
| iam.membership.role.assigned | انتصاب نقش | iam | on | high | AuditService |
| iam.membership.role.revoked | لغو نقش | iam | on | high | AuditService |
| iam.permission.override.changed | تغییر بازنویسی دسترسی | iam | on | high | AuditService |
| iam.access_request.created | ایجاد درخواست دسترسی | iam | on | high | AccessRequestService |
| iam.access_request.approved | تایید درخواست دسترسی | iam | on | high | AccessRequestService |
| iam.access_request.rejected | رد درخواست دسترسی | iam | on | high | AccessRequestService |
| security.auth.login.succeeded | ورود موفق | security | on | high | NotificationService |
| security.auth.login.failed | ورود ناموفق | security | on | high | Auth events |
| security.auth.logout | خروج | security | off | medium | NotificationService |
| security.otp.requested | درخواست OTP | security | off | high | OtpService |
| security.otp.verified | تایید OTP | security | off | high | OtpService |
| security.otp.failed | خطای OTP | security | on | high | OtpService |
| security.impersonation.started | شروع امپرسونیشن | security | on | critical | ImpersonationService |
| security.impersonation.stopped | پایان امپرسونیشن | security | on | critical | ImpersonationService |
| security.api_key.created | ایجاد کلید API | security | on | critical | AuditService |
| security.api_key.rotated | چرخش کلید API | security | on | critical | AuditService |
| security.api_key.revoked | ابطال کلید API | security | on | critical | AuditService |
| wallet.transaction.created | ایجاد تراکنش کیف پول | wallet | off | high | AuditService |
| subscription.created | ایجاد اشتراک | billing | off | medium | SubscriptionChanged |
| subscription.renewed | تمدید اشتراک | billing | off | medium | SubscriptionChanged |
| subscription.canceled | لغو اشتراک | billing | off | medium | SubscriptionChanged |
| automation.n8n.audit.run.started | شروع حسابرسی هوشمند | automation | on | low | Scheduler |
| automation.n8n.audit.run.completed | پایان حسابرسی هوشمند | automation | on | medium | Scheduler |
| automation.n8n.report.received | دریافت گزارش هوش | automation | on | medium | Inbound |
| automation.n8n.action_proposal.received | دریافت پیشنهاد اقدام | automation | on | high | Inbound |
| automation.n8n.action_proposal.approved | تایید پیشنهاد اقدام | automation | on | critical | UI approval |
| automation.n8n.action_proposal.rejected | رد پیشنهاد اقدام | automation | on | critical | UI approval |
| automation.n8n.action_proposal.executed | اجرای پیشنهاد اقدام | automation | on | critical | Manual execution |
| automation.n8n.callback.failed | شکست Callback | automation | on | high | Inbound |

Backlog (opt-in expansions):
- `webhook.delivery.failed`, `webhook.health.degraded` (health signals)
- MFA/session events and billing/wallet opt-in events from the catalog.

## Tenancy Model
- All automation records are tenant-scoped via `BelongsToTenant` and `TenantScope`.
- Outbound dispatch resolves tenant via `TenantContext` or event source model.
- Inbound callbacks require `ResolveTenant` middleware and use tenant context for storage.
- Automation webhooks are tenant-bound; no cross-tenant/global delivery.

## Threat Model & Mitigations
- Spoofed inbound callback: HMAC signature or static token verification.
- Replay attacks: nonce + timestamp (reuse `WebhookService::verifySignature`).
- PII leakage: default redaction and minimal payloads.
- Abuse/DoS: per-tenant rate limits for automation deliveries.

## Observability
- Reuse `WebhookDelivery` logs for automation health.
- Dedicated widgets for success/failure rates and latest AI report.
- Redrive/retry controls on failed deliveries.

## Scheduler
- `iam:ai-audit:run` and `iam:automation:prune` are scheduled daily by default.
- Times are configurable via `filamat-iam.automation.schedule.*`.

## Approval Policy
- AI action proposals must remain `pending` until explicitly approved by a user with `automation.actions.manage`.
- Execution is manual and permission-gated; no auto-execution.

## Data Retention
- Deliveries retention: configurable (default 30 days).
- AI reports retention: configurable (default 90 days).

## Known Baseline Failures
- اگر بدون override اجرا شود و محیط پیش‌فرض به MySQL وصل باشد، ممکن است در جدول `cache` شکست بخورد. اجرای تست با `DB_CONNECTION=sqlite DB_DATABASE=:memory: CACHE_STORE=array QUEUE_CONNECTION=sync` پایدار است.
