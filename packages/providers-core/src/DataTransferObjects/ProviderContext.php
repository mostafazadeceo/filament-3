<?php

declare(strict_types=1);

namespace Haida\ProvidersCore\DataTransferObjects;

final class ProviderContext
{
    public function __construct(
        public readonly ?int $tenantId,
        public readonly ?int $connectionId = null,
        public readonly bool $sandbox = false,
        public readonly array $meta = [],
    ) {}
}
