<?php

namespace Haida\FilamentMeetings\Filament\Pages;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Haida\FilamentAiCore\Services\AiPolicyService;
use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Services\MeetingConsentService;
use Haida\FilamentMeetings\Services\MeetingMinutesExportService;
use Haida\FilamentMeetings\Services\MeetingNotesService;
use Haida\FilamentMeetings\Services\MeetingsAiService;
use Haida\FilamentMeetings\Services\MeetingTranscriptService;
use Haida\FilamentMeetings\Services\MeetingWorkhubService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class MeetingRoomPage extends Page
{
    protected static ?string $title = 'اتاق جلسه';

    protected static ?string $slug = 'meetings/{record}/room';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament-meetings::pages.meeting-room';

    public Meeting $record;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $agendaItems = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $attendees = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $actionItems = [];

    /**
     * @var array<string, mixed> | null
     */
    public ?array $latestMinutes = null;

    public ?string $notesContent = null;

    public bool $consentRequired = false;

    public bool $consentSatisfied = false;

    public string $consentMode = 'manual';

    public string $consentMessage = '';

    public string $consentVoiceScript = '';

    public bool $showAiBanner = false;

    public int $transcriptCount = 0;

    public ?string $lastTranscriptAt = null;

    public static function canAccess(): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.manage',
            'meetings.ai.use',
            'meetings.minutes.manage',
        ]);
    }

    public function mount(int $record): void
    {
        $meeting = Meeting::query()
            ->with([
                'organizer',
                'attendees.user',
                'agendaItems.owner',
                'notes',
                'transcripts',
                'transcriptSegments',
                'minutes',
                'actionItems.assignee',
            ])
            ->findOrFail($record);

        Gate::authorize('view', $meeting);

        $this->record = $meeting;
        $this->hydrateState();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('confirm_consent')
                ->label('ثبت رضایت')
                ->icon('heroicon-o-shield-check')
                ->visible(fn () => $this->canConfirmConsent())
                ->action(function (MeetingConsentService $service) {
                    $result = $service->confirmConsent($this->record, auth()->user());

                    if (! $result['ok']) {
                        Notification::make()
                            ->title($result['message'] ?? 'ثبت رضایت ناموفق بود.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->refreshState();

                    Notification::make()
                        ->title('رضایت ثبت شد.')
                        ->success()
                        ->send();
                }),
            Action::make('generate_agenda')
                ->label('تولید دستور جلسه')
                ->icon('heroicon-o-list-bullet')
                ->visible(fn () => $this->record->ai_enabled && IamAuthorization::allows('meetings.ai.use', IamAuthorization::resolveTenantFromRecord($this->record)))
                ->action(function (MeetingsAiService $service) {
                    if ($service->shouldQueue()) {
                        $message = $service->canGenerateAgenda($this->record);
                        if ($message) {
                            Notification::make()
                                ->title($message)
                                ->danger()
                                ->send();

                            return;
                        }

                        $service->queueAgenda($this->record, auth()->user());

                        Notification::make()
                            ->title('درخواست تولید دستور جلسه در صف قرار گرفت.')
                            ->success()
                            ->send();

                        return;
                    }

                    $result = $service->generateAgenda($this->record, auth()->user());

                    if (! $result['ok']) {
                        Notification::make()
                            ->title('خطا در تولید دستور جلسه')
                            ->body($result['message'] ?? 'درخواست ناموفق بود.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->refreshState();

                    Notification::make()
                        ->title('دستور جلسه تولید شد.')
                        ->success()
                        ->send();
                }),
            Action::make('upload_transcript')
                ->label('آپلود متن جلسه')
                ->icon('heroicon-o-arrow-up-tray')
                ->visible(fn () => IamAuthorization::allows('meetings.transcript.manage', IamAuthorization::resolveTenantFromRecord($this->record)))
                ->form([
                    FileUpload::make('transcript_file')
                        ->label('فایل متن (.txt یا .md)')
                        ->disk('local')
                        ->directory('meeting-transcripts')
                        ->acceptedFileTypes(['text/plain', 'text/markdown'])
                        ->maxSize((int) config('filament-meetings.transcripts.upload_max_kb', 10240))
                        ->required(),
                    Select::make('language')
                        ->label('زبان')
                        ->options([
                            'fa' => 'فارسی',
                            'en' => 'English',
                        ])
                        ->default(config('filament-meetings.transcripts.default_language', 'fa'))
                        ->required(),
                ])
                ->action(function (array $data, MeetingTranscriptService $service) {
                    $path = (string) ($data['transcript_file'] ?? '');
                    if ($path === '') {
                        return;
                    }

                    $content = Storage::disk('local')->get($path);
                    Storage::disk('local')->delete($path);

                    $result = $service->storeTranscript($this->record, $content, (string) ($data['language'] ?? 'fa'), 'upload', auth()->user());

                    if (! $result['ok']) {
                        Notification::make()
                            ->title('ثبت متن جلسه ناموفق بود.')
                            ->body($result['message'] ?? 'درخواست ناموفق بود.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->refreshState();

                    Notification::make()
                        ->title('متن جلسه ثبت شد.')
                        ->success()
                        ->send();
                }),
            Action::make('manual_transcript')
                ->label('ثبت دستی متن')
                ->icon('heroicon-o-pencil-square')
                ->visible(fn () => IamAuthorization::allows('meetings.transcript.manage', IamAuthorization::resolveTenantFromRecord($this->record)))
                ->form([
                    Textarea::make('content')
                        ->label('متن جلسه')
                        ->rows(8)
                        ->required(),
                    Select::make('language')
                        ->label('زبان')
                        ->options([
                            'fa' => 'فارسی',
                            'en' => 'English',
                        ])
                        ->default(config('filament-meetings.transcripts.default_language', 'fa'))
                        ->required(),
                ])
                ->action(function (array $data, MeetingTranscriptService $service) {
                    $content = (string) ($data['content'] ?? '');
                    $result = $service->storeTranscript($this->record, $content, (string) ($data['language'] ?? 'fa'), 'manual', auth()->user());

                    if (! $result['ok']) {
                        Notification::make()
                            ->title('ثبت متن جلسه ناموفق بود.')
                            ->body($result['message'] ?? 'درخواست ناموفق بود.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->refreshState();

                    Notification::make()
                        ->title('متن جلسه ثبت شد.')
                        ->success()
                        ->send();
                }),
            Action::make('generate_minutes')
                ->label('تولید صورتجلسه')
                ->icon('heroicon-o-document-text')
                ->visible(fn () => $this->record->ai_enabled && IamAuthorization::allows('meetings.ai.use', IamAuthorization::resolveTenantFromRecord($this->record)))
                ->action(function (MeetingsAiService $service) {
                    if ($service->shouldQueue()) {
                        $message = $service->canGenerateMinutes($this->record, auth()->user());
                        if ($message) {
                            Notification::make()
                                ->title($message)
                                ->danger()
                                ->send();

                            return;
                        }

                        $service->queueMinutes($this->record, auth()->user());

                        Notification::make()
                            ->title('درخواست تولید صورتجلسه در صف قرار گرفت.')
                            ->success()
                            ->send();

                        return;
                    }

                    $result = $service->generateMinutes($this->record, auth()->user());

                    if (! $result['ok']) {
                        Notification::make()
                            ->title('خطا در تولید صورتجلسه')
                            ->body($result['message'] ?? 'درخواست ناموفق بود.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->refreshState();

                    Notification::make()
                        ->title('صورتجلسه تولید شد.')
                        ->success()
                        ->send();
                }),
            Action::make('export_minutes')
                ->label('خروجی صورتجلسه')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => $this->latestMinutes !== null
                    && IamAuthorization::allows('meetings.minutes.manage', IamAuthorization::resolveTenantFromRecord($this->record)))
                ->action(function (MeetingMinutesExportService $service) {
                    return $service->exportMarkdown($this->record);
                }),
            Action::make('catch_up')
                ->label('جمع‌بندی سریع')
                ->icon('heroicon-o-sparkles')
                ->visible(fn () => $this->record->ai_enabled && IamAuthorization::allows('meetings.ai.use', IamAuthorization::resolveTenantFromRecord($this->record)))
                ->action(function (MeetingsAiService $service) {
                    if ($service->shouldQueue()) {
                        $message = $service->canGenerateRecap($this->record);
                        if ($message) {
                            Notification::make()
                                ->title($message)
                                ->danger()
                                ->send();

                            return;
                        }

                        $service->queueRecap($this->record, auth()->user());

                        Notification::make()
                            ->title('درخواست جمع‌بندی در صف قرار گرفت.')
                            ->success()
                            ->send();

                        return;
                    }

                    $result = $service->generateRecap($this->record, auth()->user());

                    if (! $result['ok']) {
                        Notification::make()
                            ->title('خطا در جمع‌بندی')
                            ->body($result['message'] ?? 'درخواست ناموفق بود.')
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('جمع‌بندی آماده شد.')
                        ->body($result['summary'] ?? '')
                        ->success()
                        ->send();
                }),
            Action::make('link_action_items')
                ->label('اتصال اقدام‌ها به ورک‌هاب')
                ->icon('heroicon-o-link')
                ->visible(fn () => IamAuthorization::allows('meetings.action_items.manage', IamAuthorization::resolveTenantFromRecord($this->record))
                    && IamAuthorization::allows('workhub.work_item.manage', IamAuthorization::resolveTenantFromRecord($this->record)))
                ->form([
                    Select::make('items')
                        ->label('اقدام‌ها')
                        ->options(fn () => collect($this->actionItems)
                            ->filter(fn (array $item) => empty($item['linked_workhub_item_id']))
                            ->mapWithKeys(fn (array $item) => [$item['id'] => $item['title']])
                            ->toArray())
                        ->multiple()
                        ->required(),
                ])
                ->action(function (array $data, MeetingWorkhubService $service) {
                    $itemIds = array_map('intval', Arr::wrap($data['items'] ?? []));
                    $result = $service->linkActionItems($this->record, $itemIds, auth()->user());

                    if (! $result['ok']) {
                        Notification::make()
                            ->title('اتصال اقدام‌ها ناموفق بود.')
                            ->body($result['message'] ?? 'درخواست ناموفق بود.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->refreshState();

                    Notification::make()
                        ->title('اقدام‌ها لینک شدند.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function saveNotes(MeetingNotesService $service): void
    {
        $service->saveNotes($this->record, (string) ($this->notesContent ?? ''), auth()->user());

        $this->refreshState();

        Notification::make()
            ->title('یادداشت‌ها ذخیره شد.')
            ->success()
            ->send();
    }

    protected function refreshState(): void
    {
        $this->record->refresh();
        $this->record->load([
            'organizer',
            'attendees.user',
            'agendaItems.owner',
            'notes',
            'transcripts',
            'minutes',
            'actionItems.assignee',
        ]);
        $this->hydrateState();
    }

    protected function hydrateState(): void
    {
        $meeting = $this->record;

        $this->agendaItems = $meeting->agendaItems
            ->sortBy('sort_order')
            ->map(fn ($item) => [
                'id' => $item->getKey(),
                'title' => $item->title,
                'description' => $item->description,
                'timebox_minutes' => $item->timebox_minutes,
                'owner' => $item->owner?->name,
            ])
            ->values()
            ->toArray();

        $this->attendees = $meeting->attendees
            ->map(fn ($attendee) => [
                'id' => $attendee->getKey(),
                'name' => $attendee->name ?: $attendee->user?->name,
                'role' => $attendee->role,
                'status' => $attendee->attendance_status,
                'consent_granted_at' => $attendee->consent_granted_at?->toDateTimeString(),
            ])
            ->toArray();

        $this->actionItems = $meeting->actionItems
            ->map(fn ($item) => [
                'id' => $item->getKey(),
                'title' => $item->title,
                'description' => $item->description,
                'status' => $item->status,
                'assignee' => $item->assignee?->name,
                'due_date' => $item->due_date?->toDateString(),
                'linked_workhub_item_id' => $item->linked_workhub_item_id,
            ])
            ->toArray();

        $latestMinute = $meeting->minutes->sortByDesc('created_at')->first();
        $this->latestMinutes = $latestMinute ? [
            'overview_text' => $latestMinute->overview_text,
            'summary_markdown' => $latestMinute->summary_markdown,
            'keywords' => $latestMinute->keywords_json,
            'decisions' => $latestMinute->decisions_json,
            'risks' => $latestMinute->risks_json,
            'created_at' => $latestMinute->created_at?->toDateTimeString(),
        ] : null;

        $this->notesContent = $meeting->notes?->content_longtext ?? '';

        $policy = app(AiPolicyService::class)->resolvePolicy($meeting->tenant);
        $this->consentRequired = (bool) ($meeting->consent_required && ($policy['consent_required_meetings'] ?? true));
        $this->consentMode = (string) $meeting->consent_mode;
        $this->consentMessage = (string) config('filament-meetings.consent.message', '');
        $this->consentVoiceScript = (string) config('filament-meetings.consent.voice_script', '');
        $this->showAiBanner = (bool) ($meeting->ai_enabled && ($policy['enabled'] ?? false));

        $this->consentSatisfied = app(MeetingConsentService::class)->isConsentSatisfied($meeting, auth()->user());

        $this->transcriptCount = $meeting->transcripts->count();
        $this->lastTranscriptAt = optional($meeting->transcripts->sortByDesc('created_at')->first())?->created_at?->toDateTimeString();
    }

    protected function canConfirmConsent(): bool
    {
        if (! $this->consentRequired || $this->consentSatisfied) {
            return false;
        }

        if ($this->record->consent_mode === 'manual') {
            return IamAuthorization::allows('meetings.manage', IamAuthorization::resolveTenantFromRecord($this->record))
                || (auth()->id() && (int) auth()->id() === (int) $this->record->organizer_id);
        }

        $userId = auth()->id();
        if (! $userId) {
            return false;
        }

        return $this->record->attendees->contains('user_id', $userId);
    }
}
