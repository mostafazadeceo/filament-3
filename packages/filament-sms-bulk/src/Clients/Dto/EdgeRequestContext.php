<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Clients\Dto;

final class EdgeRequestContext
{
    public function __construct(
        public readonly string $correlationId,
        public readonly ?int $tenantId,
        public readonly ?int $providerConnectionId,
    ) {}
}
