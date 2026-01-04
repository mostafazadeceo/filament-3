<?php

namespace Haida\FilamentPayments\Services;

use Haida\FilamentPayments\Contracts\PaymentProviderInterface;
use InvalidArgumentException;

class PaymentProviderRegistry
{
    /** @var array<string, PaymentProviderInterface> */
    protected array $providers = [];

    public function register(PaymentProviderInterface $provider): void
    {
        $this->providers[$provider->key()] = $provider;
    }

    public function get(string $key): PaymentProviderInterface
    {
        if (! array_key_exists($key, $this->providers)) {
            throw new InvalidArgumentException('Unknown payment provider: '.$key);
        }

        return $this->providers[$key];
    }

    /**
     * @return array<string, PaymentProviderInterface>
     */
    public function all(): array
    {
        return $this->providers;
    }
}
