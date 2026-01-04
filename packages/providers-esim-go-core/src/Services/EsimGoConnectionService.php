<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Services;

use Haida\ProvidersEsimGoCore\Clients\EsimGoClientFactory;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Throwable;

class EsimGoConnectionService
{
    public function __construct(
        protected EsimGoClientFactory $clientFactory,
    ) {}

    public function testConnection(EsimGoConnection $connection, bool $sandbox = false): bool
    {
        try {
            $client = $this->clientFactory->make($connection, $sandbox);
            $client->listCatalogue([
                'perPage' => 1,
                '__nocache' => true,
            ]);

            $connection->update([
                'status' => 'active',
                'last_tested_at' => now(),
            ]);

            return true;
        } catch (Throwable) {
            $connection->update([
                'status' => 'inactive',
                'last_tested_at' => now(),
            ]);

            return false;
        }
    }
}
