<?php

namespace Haida\FilamentCryptoCore\Services;

use Illuminate\Contracts\Events\Dispatcher;

class CryptoEventBus
{
    public function __construct(protected Dispatcher $dispatcher) {}

    public function dispatch(object $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}
