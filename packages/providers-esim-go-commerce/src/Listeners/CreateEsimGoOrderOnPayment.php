<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCommerce\Listeners;

use Haida\CommerceOrders\Events\OrderPaid;
use Haida\ProvidersEsimGoCommerce\Services\EsimGoCommerceService;

class CreateEsimGoOrderOnPayment
{
    public function __construct(
        protected EsimGoCommerceService $service,
    ) {}

    public function handle(OrderPaid $event): void
    {
        $this->service->createProviderOrderForCommerceOrder($event->order);
    }
}
