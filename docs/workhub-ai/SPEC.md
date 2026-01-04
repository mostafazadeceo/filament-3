# SPEC — Workhub AI Extension

## اهداف
- افزودن قابلیت‌های هوش مصنوعی به Workhub بدون شکستن رفتار فعلی.
- رعایت کامل چندتننتی، مجوزها، و شفافیت AI (بدون نظارت پنهان).
- استفاده از معماری پکیج و AI Core مشترک.

## عدم‌اهداف
- هرگونه مانیتورینگ پنهان یا ضبط مخفی.
- اقدام خودکار مخرب بدون تایید انسان.

## وابستگی‌ها
- `haida/filament-workhub` (پکیج موجود).
- `filamat/filamat-iam-suite` (IAM، tenancy، API middleware).
- `haida/filament-notify-core` برای اعلان‌ها.
- پکیج مشترک `haida/filament-ai-core`.

## دامنه (افزوده‌ها)
- جدول‌های AI در Workhub با پیشوند `workhub_ai_`.
- UI: تب «هوش مصنوعی» در Work Item + گزارش‌های پروژه.
- API: مسیرهای `/api/v1/workhub/.../ai/*` با middleware استاندارد.
- وبهوک‌ها: `workhub.ai.*` با envelope استاندارد.

## مجوزها
- workhub.ai.use
- workhub.ai.share
- workhub.ai.fields.manage
- workhub.ai.project_reports.manage

## مسیرهای API (High-level)
- POST `/work-items/{id}/ai/personal-summary`
- POST `/work-items/{id}/ai/shared-summary`
- POST `/work-items/{id}/ai/thread-summary`
- POST `/work-items/{id}/ai/generate-subtasks`
- POST `/work-items/{id}/ai/progress-update`
- POST `/work-items/{id}/ai/find-similar`
- POST `/projects/{id}/ai/executive-summary`
- GET  `/projects/{id}/ai/stuck-tasks`

## وبهوک‌ها
- workhub.ai.summary.created
- workhub.ai.subtasks.created
- workhub.ai.field.generated
- workhub.ai.project_report.created

## تست‌ها (حداقلی)
- ایزولیشن دو تننت.
- مجوزها برای همه endpointها/اکشن‌ها.
- MockAiProvider deterministic.
- جریان نمونه: task -> comments -> personal summary -> shared summary.
- اجرای bulk AI fields.

## Fallback Ladder
- Provider واقعی شکست خورد -> Mock provider + feature flag.
- Semantic search سنگین -> Full-text search فعلا.
- Speaker attribution ناقص -> حذف «who-said-what».

## چک‌لیست Milestones
- [x] Milestone 0: Repo Scan + Docs
  - ✅ فایل‌های REPO_FINDINGS و SPEC/INSTALL/API ایجاد شوند.
- [x] Milestone 1: AI Core Package
  - ✅ Contract/Providers/Governance + Tests
- [x] Milestone 2: Workhub AI
  - ✅ DB + UI + API + Events + Tests + Docs
- [x] Milestone 3: Meetings Plugin (وابسته)
  - ✅ امکان لینک action items به Workhub
- [x] Milestone 4: Hardening/Performance
  - ✅ Queue, rate limit, circuit breaker
- [x] Milestone 5: Scenario Runner + Final Review
  - ✅ deep_scenario_runner + تست کامل
