<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Contracts;

interface IamEvent
{
    public function eventName(): string;

    /**
     * @return array<string, mixed>
     */
    public function payload(): array;

    public function tenantId(): ?int;
}
