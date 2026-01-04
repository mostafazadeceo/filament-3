<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class SubscriptionCanceled extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'subscription.canceled';
    }
}
