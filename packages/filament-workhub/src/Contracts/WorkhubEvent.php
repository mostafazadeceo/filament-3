<?php

namespace Haida\FilamentWorkhub\Contracts;

interface WorkhubEvent
{
    public function eventName(): string;

    /**
     * @return array<string, mixed>
     */
    public function payload(): array;

    public function tenantId(): ?int;
}
