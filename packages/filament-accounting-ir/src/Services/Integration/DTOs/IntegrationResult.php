<?php

namespace Vendor\FilamentAccountingIr\Services\Integration\DTOs;

class IntegrationResult
{
    /**
     * @param  array<string, mixed>  $summary
     * @param  array<int, array<string, mixed>>  $logs
     */
    public function __construct(
        public bool $success,
        public array $summary = [],
        public array $logs = [],
    ) {}
}
