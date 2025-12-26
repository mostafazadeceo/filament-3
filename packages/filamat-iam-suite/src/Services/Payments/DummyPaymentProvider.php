<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Payments;

use Filamat\IamSuite\Contracts\PaymentProviderInterface;
use Filamat\IamSuite\Models\Subscription;

class DummyPaymentProvider implements PaymentProviderInterface
{
    public function name(): string
    {
        return 'dummy';
    }

    public function createCheckout(Subscription $subscription, array $payload = []): array
    {
        return [
            'status' => 'ok',
            'provider' => $this->name(),
            'subscription_id' => $subscription->getKey(),
        ];
    }

    public function handleWebhook(array $payload, array $headers = []): void
    {
        // Dummy provider does not process webhooks.
    }
}
