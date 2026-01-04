<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Automation;

use Filamat\IamSuite\Contracts\IamEvent;

class IamEventPublisher
{
    public function __construct(protected IamWebhookDispatcher $dispatcher) {}

    public function publish(IamEvent $event): void
    {
        if (! (bool) config('filamat-iam.automation.enabled', true)) {
            return;
        }

        $this->dispatcher->dispatch($event);
    }
}
