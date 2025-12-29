<?php

namespace Haida\FilamentWorkhub\Listeners;

use Haida\FilamentWorkhub\Events\AttachmentCreated;
use Haida\FilamentWorkhub\Events\CommentCreated;
use Haida\FilamentWorkhub\Events\ProjectCreated;
use Haida\FilamentWorkhub\Events\ProjectUpdated;
use Haida\FilamentWorkhub\Events\WorkItemCreated;
use Haida\FilamentWorkhub\Events\WorkItemTransitioned;
use Haida\FilamentWorkhub\Events\WorkItemUpdated;
use Haida\FilamentWorkhub\Services\WorkhubAutomationEngine;
use Haida\FilamentWorkhub\Services\WorkhubWebhookDispatcher;
use Illuminate\Events\Dispatcher;

class WorkhubEventSubscriber
{
    public function __construct(
        protected WorkhubWebhookDispatcher $dispatcher,
        protected WorkhubAutomationEngine $automationEngine,
    )
    {
    }

    public function handleProjectCreated(ProjectCreated $event): void
    {
        $this->dispatcher->dispatch($event);
        $this->automationEngine->handle($event->eventName(), $event->payload());
    }

    public function handleProjectUpdated(ProjectUpdated $event): void
    {
        $this->dispatcher->dispatch($event);
        $this->automationEngine->handle($event->eventName(), $event->payload());
    }

    public function handleWorkItemCreated(WorkItemCreated $event): void
    {
        $this->dispatcher->dispatch($event);
        $this->automationEngine->handle($event->eventName(), $event->payload());
    }

    public function handleWorkItemUpdated(WorkItemUpdated $event): void
    {
        $this->dispatcher->dispatch($event);
        $this->automationEngine->handle($event->eventName(), $event->payload());
    }

    public function handleWorkItemTransitioned(WorkItemTransitioned $event): void
    {
        $this->dispatcher->dispatch($event);
        $this->automationEngine->handle($event->eventName(), $event->payload());
    }

    public function handleCommentCreated(CommentCreated $event): void
    {
        $this->dispatcher->dispatch($event);
        $this->automationEngine->handle($event->eventName(), $event->payload());
    }

    public function handleAttachmentCreated(AttachmentCreated $event): void
    {
        $this->dispatcher->dispatch($event);
        $this->automationEngine->handle($event->eventName(), $event->payload());
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(ProjectCreated::class, [self::class, 'handleProjectCreated']);
        $events->listen(ProjectUpdated::class, [self::class, 'handleProjectUpdated']);
        $events->listen(WorkItemCreated::class, [self::class, 'handleWorkItemCreated']);
        $events->listen(WorkItemUpdated::class, [self::class, 'handleWorkItemUpdated']);
        $events->listen(WorkItemTransitioned::class, [self::class, 'handleWorkItemTransitioned']);
        $events->listen(CommentCreated::class, [self::class, 'handleCommentCreated']);
        $events->listen(AttachmentCreated::class, [self::class, 'handleAttachmentCreated']);
    }
}
