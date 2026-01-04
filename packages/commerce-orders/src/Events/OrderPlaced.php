<?php

namespace Haida\CommerceOrders\Events;

use Haida\CommerceOrders\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Order $order)
    {
    }
}
