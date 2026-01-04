<?php

namespace Haida\FilamentMeetings\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentMeetings\Policies\MeetingActionItemPolicy;
use Haida\FilamentMeetings\Policies\MeetingAgendaItemPolicy;
use Haida\FilamentMeetings\Policies\MeetingAttendeePolicy;
use Haida\FilamentMeetings\Policies\MeetingMinutePolicy;
use Haida\FilamentMeetings\Policies\MeetingNotePolicy;
use Haida\FilamentMeetings\Policies\MeetingPolicy;
use Haida\FilamentMeetings\Policies\MeetingTemplatePolicy;
use Haida\FilamentMeetings\Policies\MeetingTranscriptPolicy;

final class MeetingsCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-meetings',
            self::permissions(),
            [
                'meetings' => true,
            ],
            [],
            [
                MeetingPolicy::class,
                MeetingTemplatePolicy::class,
                MeetingAttendeePolicy::class,
                MeetingAgendaItemPolicy::class,
                MeetingNotePolicy::class,
                MeetingTranscriptPolicy::class,
                MeetingMinutePolicy::class,
                MeetingActionItemPolicy::class,
            ],
            [
                'meetings' => 'جلسات',
                'meetings_templates' => 'قالب‌های جلسه',
                'meetings_action_items' => 'اقدام‌های جلسه',
                'meetings_ai' => 'هوش مصنوعی جلسات',
            ]
        );

        self::$registered = true;
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'meetings.view',
            'meetings.manage',
            'meetings.templates.manage',
            'meetings.transcript.manage',
            'meetings.minutes.manage',
            'meetings.ai.use',
            'meetings.ai.manage',
            'meetings.action_items.manage',
            'meetings.share.manage',
        ];
    }
}
