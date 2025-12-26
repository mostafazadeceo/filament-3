<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Contracts\PaymentProviderInterface;
use Filamat\IamSuite\Services\Payments\DummyPaymentProvider;
use InvalidArgumentException;

class PaymentProviderManager
{
    public function driver(): PaymentProviderInterface
    {
        $driver = (string) config('filamat-iam.payment_provider', 'dummy');

        return match ($driver) {
            'dummy' => app(DummyPaymentProvider::class),
            default => throw new InvalidArgumentException('Payment provider is not supported.'),
        };
    }
}
