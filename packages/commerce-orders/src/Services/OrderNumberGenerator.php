<?php

namespace Haida\CommerceOrders\Services;

class OrderNumberGenerator
{
    public function generate(int $orderId): string
    {
        $prefix = (string) config('commerce-orders.numbers.prefix', 'ORD-');
        $pad = (int) config('commerce-orders.numbers.pad', 8);

        return $prefix.str_pad((string) $orderId, $pad, '0', STR_PAD_LEFT);
    }
}
