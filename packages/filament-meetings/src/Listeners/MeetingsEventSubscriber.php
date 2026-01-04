<?php

namespace Haida\FilamentMeetings\Listeners;

use Haida\FilamentMeetings\Events\MeetingActionItemCreated;
use Haida\FilamentMeetings\Events\MeetingActionItemLinkedToWorkhub;
use Haida\FilamentMeetings\Events\MeetingAiAgendaGenerated;
use Haida\FilamentMeetings\Events\MeetingAiMinutesGenerated;
use Haida\FilamentMeetings\Events\MeetingCompleted;
use Haida\FilamentMeetings\Events\MeetingConsentConfirmed;
use Haida\FilamentMeetings\Events\MeetingCreated;
use Haida\FilamentMeetings\Events\MeetingUpdated;
use Haida\FilamentMeetings\Services\MeetingsWebhookDispatcher;
use Illuminate\Events\Dispatcher;

class MeetingsEventSubscriber
{
    public function __construct(protected MeetingsWebhookDispatcher $dispatcher) {}

    public function handleMeetingCreated(MeetingCreated $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handleMeetingUpdated(MeetingUpdated $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handleMeetingCompleted(MeetingCompleted $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handleConsentConfirmed(MeetingConsentConfirmed $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handleAiAgendaGenerated(MeetingAiAgendaGenerated $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handleAiMinutesGenerated(MeetingAiMinutesGenerated $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handleActionItemCreated(MeetingActionItemCreated $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function handleActionItemLinkedToWorkhub(MeetingActionItemLinkedToWorkhub $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(MeetingCreated::class, [self::class, 'handleMeetingCreated']);
        $events->listen(MeetingUpdated::class, [self::class, 'handleMeetingUpdated']);
        $events->listen(MeetingCompleted::class, [self::class, 'handleMeetingCompleted']);
        $events->listen(MeetingConsentConfirmed::class, [self::class, 'handleConsentConfirmed']);
        $events->listen(MeetingAiAgendaGenerated::class, [self::class, 'handleAiAgendaGenerated']);
        $events->listen(MeetingAiMinutesGenerated::class, [self::class, 'handleAiMinutesGenerated']);
        $events->listen(MeetingActionItemCreated::class, [self::class, 'handleActionItemCreated']);
        $events->listen(MeetingActionItemLinkedToWorkhub::class, [self::class, 'handleActionItemLinkedToWorkhub']);
    }
}
