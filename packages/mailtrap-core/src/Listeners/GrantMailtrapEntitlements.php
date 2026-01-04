<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Listeners;

use Haida\CommerceOrders\Events\OrderPaid;
use Haida\MailtrapCore\Services\MailtrapOfferService;

class GrantMailtrapEntitlements
{
    public function __construct(
        protected MailtrapOfferService $offers,
    ) {}

    public function handle(OrderPaid $event): void
    {
        $this->offers->grantEntitlementsFromOrder($event->order);
    }
}
