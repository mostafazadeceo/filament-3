# Apps Spec (Android + Web/PWA)

## North star
Build two production-grade clients (Android Native + Web/PWA) for the existing Filament multi-tenant SaaS. Clients are offline-first, tenant-scoped, permission-driven, Persian-first (RTL + Jalali), and integrate with IAM suite, notifications, and OpenAPI docs.

## Repo scan highlights
- Backend stack: Laravel 12.x (meets 11.28+), Filament v4, Sanctum, Spatie permissions with teams, Filamat IAM Suite.
- API docs builder: `zpmlabs/filament-api-docs-builder` enabled in both panels.
- Existing modules with APIs + OpenAPI endpoints:
  - POS: `packages/filament-pos` (`/api/v1/filament-pos/openapi`)
  - Payroll/Attendance: `packages/filament-payroll-attendance-ir` (`/api/v1/payroll-attendance/openapi`)
  - Workhub (tasks): `packages/filament-workhub` (`/api/v1/workhub/openapi`)
  - Meetings: `packages/filament-meetings` (`/api/v1/meetings/openapi`)
  - Crypto gateway: `packages/filament-crypto-gateway` (`/api/v1/crypto/openapi`)
  - Loyalty club: `packages/filament-loyalty-club` (`/api/v1/loyalty/openapi`)
  - Payments/commerce: `packages/filament-payments`, `packages/commerce-*`
- IAM Suite provides API auth middleware (`ApiKeyAuth`, `ApiAuth`), tenant resolution, and permission scoping.

## New module required
A dedicated app-facing API package is added to fill mobile/web gaps:
- `packages/filament-app-api`
- Purpose: app auth/token flow, tenant switch, permissions/capabilities, app config, device/push tokens, realtime signaling metadata, offline sync, in-app notification feed, and support/ticketing endpoints.
- Table prefix: `app_`.
- All endpoints tenant-scoped + permissioned.

## Client apps
- Web/PWA: Next.js (App Router) + TypeScript + Tailwind + shadcn/ui + React Query + Dexie (IndexedDB).
- Android: Kotlin + Jetpack Compose + Hilt + Retrofit/OkHttp + Room.
- Realtime: WebSocket with polling fallback.
- Push: FCM (Android), Web Push/FCM if available otherwise in-app.
- WebRTC: 1:1 calls, fallback to voice-note + call log.

## API contract plan (OpenAPI-first)
- New App API spec exposed via `/api/v1/app/openapi`.
- Clients generate SDKs via OpenAPI Generator and store results inside app repos:
  - `apps/web-pwa/src/lib/api-client/`
  - `apps/mobile-android/app/src/main/java/.../api/`
- Existing module OpenAPI specs remain source-of-truth for POS, HR, Workhub, Meetings, Crypto, Loyalty.

## Endpoint map (minimum)
- Auth: `POST /api/v1/app/auth/login`, `POST /api/v1/app/auth/refresh`, `POST /api/v1/app/auth/logout`, `GET /api/v1/app/auth/me`
- Tenant: `GET /api/v1/app/tenant/current`, `POST /api/v1/app/tenant/switch`
- Permissions: `GET /api/v1/app/capabilities`
- App config: `GET /api/v1/app/config`
- Sync: `POST /api/v1/app/sync/push`, `GET /api/v1/app/sync/pull`, `POST /api/v1/app/sync/conflicts`
- Device/push: `POST /api/v1/app/devices`, `POST /api/v1/app/devices/{device}/tokens`, `DELETE /api/v1/app/devices/{device}`
- Notifications feed: `GET /api/v1/app/notifications`, `POST /api/v1/app/notifications/{id}/read`
- Support: `GET/POST /api/v1/app/tickets`, `GET/POST /api/v1/app/tickets/{ticket}/messages`, `POST /api/v1/app/tickets/{ticket}/attachments`
- Realtime signaling: `GET/POST /api/v1/app/realtime/signals`

## Offline-first sync strategy (shared)
- Local-first, server-authoritative, conflict-aware.
- Outbox queues write operations; sync workers retry with exponential backoff.
- Conflict policies (hard requirement):
  - POS order immutable after finalize, otherwise LWW + audit.
  - Chat/ticket messages append-only.
  - Attendance immutable with correction workflow.
  - Tasks LWW + audit.
- Sync cursor is server-issued (timestamp-based cursor used when change feed not supported).

## Security + privacy guardrails
- Tokens stored securely (Android Keystore + Web HttpOnly cookies/IndexedDB encryption when needed).
- BiometricPrompt for session unlock and sensitive actions.
- Play Integrity token supported behind feature flag.
- Location/face/camera only opt-in per permission; no continuous surveillance.
- Push payloads never include sensitive content; use “update available” pattern.

## Decisions & assumptions (recorded due to ambiguity)
- Auth uses email/password with Sanctum tokens; refresh issues a new token and revokes the old.
- Tenant selection is explicit in login request or via `tenant/switch` to issue a tenant-bound token.
- Sync cursor is ISO8601 timestamp; pull returns `next_cursor` based on max updated_at seen.
- Support module is implemented in `filament-app-api` (tickets/messages/attachments) because no dedicated support package exists in repo.
- WebRTC TURN/STUN configuration is provided via app config endpoint and may be empty in dev.
- App sync v1 records staged changes in `app_sync_changes`; deep domain write-backs (POS/HR/Workhub) are backlog until server-side adapters are added.

## Backlog (initial)
- Extend change feed for existing modules to include richer delta payloads.
- Add full OpenAPI schemas for all app-api request/response types.
- Add push delivery feedback metrics (per device).
- Add realtime bridging for support ticket events.
