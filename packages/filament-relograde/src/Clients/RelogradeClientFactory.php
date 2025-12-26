<?php

namespace Haida\FilamentRelograde\Clients;

use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Services\RelogradeApiLogger;
use Haida\FilamentRelograde\Support\RelogradeRateLimiter;

class RelogradeClientFactory
{
    public function __construct(
        protected RelogradeRateLimiter $rateLimiter,
        protected RelogradeApiLogger $logger,
    ) {}

    public function make(RelogradeConnection $connection): RelogradeClient
    {
        return new RelogradeClient($connection, $this->rateLimiter, $this->logger);
    }
}
