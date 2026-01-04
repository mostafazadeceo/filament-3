<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCommerce\Listeners;

use Haida\ProvidersEsimGoCommerce\Services\EsimGoCommerceService;
use Haida\ProvidersEsimGoCore\Events\EsimGoOrderReady;

class ApplyEsimGoFulfillment
{
    public function __construct(
        protected EsimGoCommerceService $service,
    ) {}

    public function handle(EsimGoOrderReady $event): void
    {
        $this->service->applyFulfillment($event->order);
    }
}
