<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCommerce\Listeners;

use Haida\ProvidersEsimGoCommerce\Services\EsimGoCommerceService;
use Haida\ProvidersEsimGoCore\Events\EsimGoCatalogueSynced;

class SyncEsimGoCatalogueToCommerce
{
    public function __construct(
        protected EsimGoCommerceService $service,
    ) {}

    public function handle(EsimGoCatalogueSynced $event): void
    {
        $this->service->syncCatalogueToCommerce($event->connection);
    }
}
