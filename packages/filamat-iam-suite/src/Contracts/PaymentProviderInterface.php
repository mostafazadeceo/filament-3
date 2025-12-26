<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Contracts;

use Filamat\IamSuite\Models\Subscription;

interface PaymentProviderInterface
{
    public function name(): string;

    public function createCheckout(Subscription $subscription, array $payload = []): array;

    public function handleWebhook(array $payload, array $headers = []): void;
}
