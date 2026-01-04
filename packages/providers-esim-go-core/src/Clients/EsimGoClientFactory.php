<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Clients;

use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Haida\ProvidersEsimGoCore\Support\EsimGoRateLimiter;

class EsimGoClientFactory
{
    public function __construct(
        protected EsimGoRateLimiter $rateLimiter,
    ) {}

    public function make(EsimGoConnection $connection, bool $sandbox = false): EsimGoClient
    {
        return new EsimGoClient($connection, $this->rateLimiter, $sandbox);
    }
}
