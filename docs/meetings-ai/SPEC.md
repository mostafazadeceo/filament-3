# SPEC — Meetings AI Plugin

## اهداف
- ارائه ماژول جلسات با هوش مصنوعی (صورتجلسه/دستور جلسه) با رضایت صریح.
- رعایت کامل چندتننتی، مجوزها، شفافیت AI و عدم نظارت پنهان.
- ادغام با Workhub برای تبدیل action items به work items.

## عدم‌اهداف
- ضبط/شنود مخفی یا هر نوع نظارت بدون اطلاع.
- تولید صورتجلسه بدون رضایت یا بدون بنر شفافیت.

## وابستگی‌ها
- `filamat/filamat-iam-suite` (IAM/tenancy/API).
- `haida/filament-notify-core` (اعلان‌ها).
- `haida/filament-ai-core` (AI providers + governance).
- `haida/filament-workhub` (لینک action items).

## دامنه (مدل‌ها)
- meetings, meeting_attendees, meeting_templates, meeting_agenda_items
- meeting_notes, meeting_transcripts, meeting_transcript_segments
- meeting_minutes, meeting_action_items, meeting_ai_runs

## رضایت و شفافیت
- ذخیره رضایت در دیتابیس.
- بنر «AI فعال است» در UI.
- ثبت هر اجرای AI در ai_requests + audit logs.

## مجوزها
- meetings.view
- meetings.manage
- meetings.templates.manage
- meetings.transcript.manage
- meetings.minutes.manage
- meetings.ai.use
- meetings.ai.manage
- meetings.action_items.manage
- meetings.share.manage

## API (High-level)
- CRUD meetings/templates/attendees/agenda
- POST `/meetings/{id}/consent/confirm`
- POST `/meetings/{id}/transcript/upload`
- POST `/meetings/{id}/transcript/manual`
- POST `/meetings/{id}/ai/generate-agenda`
- POST `/meetings/{id}/ai/generate-minutes`
- POST `/meetings/{id}/ai/recap`
- GET  `/meetings/{id}/minutes/export`
- POST `/meetings/{id}/action-items/link-to-workhub`

## وبهوک‌ها
- meetings.created/updated/completed
- meetings.consent.confirmed
- meetings.ai.agenda.generated
- meetings.ai.minutes.generated
- meetings.action_item.created
- meetings.action_item.linked_to_workhub

## تست‌ها (حداقلی)
- ایزولیشن دو تننت.
- Consent gate tests.
- Integration flow با Workhub.
- Failure path: provider fails -> manual minutes draft.

## Fallback Ladder
- Provider واقعی شکست خورد -> Mock provider + feature flag.
- Live transcription پیچیده -> فقط manual/upload + hook skeleton.
- Semantic search پیچیده -> full-text فعلا.

## چک‌لیست Milestones
- [x] Milestone 0: Repo Scan + Docs
- [x] Milestone 1: AI Core Package
- [x] Milestone 2: Workhub AI
- [x] Milestone 3: Meetings Plugin
- [x] Milestone 4: Hardening/Performance
- [x] Milestone 5: Scenario Runner + Final Review
