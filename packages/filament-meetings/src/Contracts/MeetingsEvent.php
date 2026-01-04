<?php

namespace Haida\FilamentMeetings\Contracts;

interface MeetingsEvent
{
    public function eventName(): string;

    /**
     * @return array<string, mixed>
     */
    public function payload(): array;

    public function tenantId(): ?int;
}
