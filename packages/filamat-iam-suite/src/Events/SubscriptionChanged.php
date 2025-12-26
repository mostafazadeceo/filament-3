<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events;

use Filamat\IamSuite\Models\Subscription;

class SubscriptionChanged
{
    public function __construct(public readonly Subscription $subscription) {}
}
