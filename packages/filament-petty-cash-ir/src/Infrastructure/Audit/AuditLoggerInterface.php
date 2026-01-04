<?php

namespace Haida\FilamentPettyCashIr\Infrastructure\Audit;

interface AuditLoggerInterface
{
    public function log(object $subject, ?int $actorId, string $eventType, ?string $description = null, array $metadata = []): void;
}
