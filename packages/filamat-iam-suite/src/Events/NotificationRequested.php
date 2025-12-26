<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events;

class NotificationRequested
{
    public function __construct(
        public readonly mixed $target,
        public readonly string $type,
        public readonly array $payload = [],
    ) {}
}
