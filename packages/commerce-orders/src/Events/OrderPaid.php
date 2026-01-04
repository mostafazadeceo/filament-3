<?php

namespace Haida\CommerceOrders\Events;

use Haida\CommerceOrders\Models\Order;
use Haida\CommerceOrders\Models\OrderPayment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPaid
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Order $order, public OrderPayment $payment)
    {
    }
}
