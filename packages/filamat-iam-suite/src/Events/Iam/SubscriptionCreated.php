<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class SubscriptionCreated extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'subscription.created';
    }
}
