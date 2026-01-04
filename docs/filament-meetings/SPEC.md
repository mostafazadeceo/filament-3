# SPEC — filament-meetings

## معرفی
- پکیج: haida/filament-meetings
- توضیح: Meetings module with AI-assisted agenda and minutes for Filament v4.
- Service Provider: Haida\FilamentMeetings\FilamentMeetingsServiceProvider
- Filament Plugin: Haida\FilamentMeetings\FilamentMeetingsPlugin (id: meetings)

## دامنه و قابلیت‌ها
- مدل‌ها:
- Meeting.php
- MeetingActionItem.php
- MeetingAgendaItem.php
- MeetingAiRun.php
- MeetingAttendee.php
- MeetingMinute.php
- MeetingNote.php
- MeetingTemplate.php
- MeetingTranscript.php
- MeetingTranscriptSegment.php
- منابع Filament:
- src/Filament/Resources/MeetingResource.php
- src/Filament/Resources/MeetingTemplateResource.php
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/MeetingActionItemController.php
- Api/V1/MeetingAgendaItemController.php
- Api/V1/MeetingAiController.php
- Api/V1/MeetingAttendeeController.php
- Api/V1/MeetingConsentController.php
- Api/V1/MeetingController.php
- Api/V1/MeetingMinutesController.php
- Api/V1/MeetingTemplateController.php
- Api/V1/MeetingTranscriptController.php
- Api/V1/OpenApiController.php
- Jobs/Queue:
- GenerateMeetingAgendaJob.php
- GenerateMeetingMinutesJob.php
- GenerateMeetingRecapJob.php
- Policyها:
- MeetingActionItemPolicy.php
- MeetingAgendaItemPolicy.php
- MeetingAttendeePolicy.php
- MeetingMinutePolicy.php
- MeetingNotePolicy.php
- MeetingPolicy.php
- MeetingTemplatePolicy.php
- MeetingTranscriptPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): meetings.action_items.manage, meetings.ai.use, meetings.manage, meetings.minutes.manage, meetings.transcript.manage, meetings.view

## مدل داده
- Migrations:
- 2026_03_01_000001_create_meetings_table.php
- 2026_03_01_000002_create_meeting_attendees_table.php
- 2026_03_01_000003_create_meeting_templates_table.php
- 2026_03_01_000004_create_meeting_agenda_items_table.php
- 2026_03_01_000005_create_meeting_notes_table.php
- 2026_03_01_000006_create_meeting_transcripts_table.php
- 2026_03_01_000007_create_meeting_transcript_segments_table.php
- 2026_03_01_000008_create_meeting_minutes_table.php
- 2026_03_01_000009_create_meeting_action_items_table.php
- 2026_03_01_000010_create_meeting_ai_runs_table.php
- 2026_03_01_000011_add_meeting_indexes_table.php
- جدول‌ها:
- meeting_action_items
- meeting_agenda_items
- meeting_ai_runs
- meeting_attendees
- meeting_minutes
- meeting_notes
- meeting_templates
- meeting_transcript_segments
- meeting_transcripts
- meetings
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: , v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/filament-meetings/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-meetings/config/filament-meetings.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت نشده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
