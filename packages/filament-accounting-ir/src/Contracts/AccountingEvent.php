<?php

namespace Vendor\FilamentAccountingIr\Contracts;

interface AccountingEvent
{
    public function eventName(): string;

    /**
     * @return array<string, mixed>
     */
    public function payload(): array;

    public function tenantId(): ?int;
}
