<?php

namespace Haida\FilamentMeetings;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentMeetings\Console\Commands\SendMeetingsDigest;
use Haida\FilamentMeetings\Filament\Resources\MeetingResource;
use Haida\FilamentMeetings\Listeners\MeetingsEventSubscriber;
use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Models\MeetingActionItem;
use Haida\FilamentMeetings\Models\MeetingAgendaItem;
use Haida\FilamentMeetings\Models\MeetingAttendee;
use Haida\FilamentMeetings\Models\MeetingMinute;
use Haida\FilamentMeetings\Models\MeetingNote;
use Haida\FilamentMeetings\Models\MeetingTemplate;
use Haida\FilamentMeetings\Models\MeetingTranscript;
use Haida\FilamentMeetings\Policies\MeetingActionItemPolicy;
use Haida\FilamentMeetings\Policies\MeetingAgendaItemPolicy;
use Haida\FilamentMeetings\Policies\MeetingAttendeePolicy;
use Haida\FilamentMeetings\Policies\MeetingMinutePolicy;
use Haida\FilamentMeetings\Policies\MeetingNotePolicy;
use Haida\FilamentMeetings\Policies\MeetingPolicy;
use Haida\FilamentMeetings\Policies\MeetingTemplatePolicy;
use Haida\FilamentMeetings\Policies\MeetingTranscriptPolicy;
use Haida\FilamentMeetings\Support\MeetingsCapabilities;
use Haida\FilamentWorkhub\Support\EntityReferenceRegistry;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMeetingsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-meetings')
            ->hasConfigFile('filament-meetings')
            ->hasViews()
            ->hasTranslations()
            ->hasRoutes('api')
            ->hasMigrations([
                '2026_03_01_000001_create_meetings_table',
                '2026_03_01_000002_create_meeting_attendees_table',
                '2026_03_01_000003_create_meeting_templates_table',
                '2026_03_01_000004_create_meeting_agenda_items_table',
                '2026_03_01_000005_create_meeting_notes_table',
                '2026_03_01_000006_create_meeting_transcripts_table',
                '2026_03_01_000007_create_meeting_transcript_segments_table',
                '2026_03_01_000008_create_meeting_minutes_table',
                '2026_03_01_000009_create_meeting_action_items_table',
                '2026_03_01_000010_create_meeting_ai_runs_table',
                '2026_03_01_000011_add_meeting_indexes_table',
            ])
            ->hasCommands([
                SendMeetingsDigest::class,
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Services\MeetingsWebhookDispatcher::class);
    }

    public function packageBooted(): void
    {
        Gate::policy(Meeting::class, MeetingPolicy::class);
        Gate::policy(MeetingTemplate::class, MeetingTemplatePolicy::class);
        Gate::policy(MeetingAttendee::class, MeetingAttendeePolicy::class);
        Gate::policy(MeetingAgendaItem::class, MeetingAgendaItemPolicy::class);
        Gate::policy(MeetingNote::class, MeetingNotePolicy::class);
        Gate::policy(MeetingTranscript::class, MeetingTranscriptPolicy::class);
        Gate::policy(MeetingMinute::class, MeetingMinutePolicy::class);
        Gate::policy(MeetingActionItem::class, MeetingActionItemPolicy::class);

        Meeting::observe(Observers\MeetingObserver::class);
        MeetingActionItem::observe(Observers\MeetingActionItemObserver::class);

        Event::subscribe(MeetingsEventSubscriber::class);

        $registry = $this->app->make(CapabilityRegistryInterface::class);
        MeetingsCapabilities::register($registry);

        if (class_exists(EntityReferenceRegistry::class)) {
            $linkRegistry = $this->app->make(EntityReferenceRegistry::class);
            $linkRegistry->register(
                'meetings.meeting',
                Meeting::class,
                'جلسه',
                'heroicon-o-video-camera',
                fn (Meeting $meeting) => MeetingResource::getUrl('edit', ['record' => $meeting]),
                fn (Meeting $meeting) => $meeting->title,
            );
        }
    }
}
