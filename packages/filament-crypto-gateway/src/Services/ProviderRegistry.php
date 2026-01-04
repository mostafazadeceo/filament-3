<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Services;

use Haida\FilamentCryptoGateway\Contracts\ProviderAdapterInterface;
use InvalidArgumentException;

class ProviderRegistry
{
    /** @var array<string, ProviderAdapterInterface> */
    protected array $providers = [];

    public function register(ProviderAdapterInterface $provider): void
    {
        $this->providers[$provider->key()] = $provider;
    }

    public function get(string $key): ProviderAdapterInterface
    {
        if (! array_key_exists($key, $this->providers)) {
            throw new InvalidArgumentException('Unknown crypto provider: '.$key);
        }

        return $this->providers[$key];
    }

    /**
     * @return array<string, ProviderAdapterInterface>
     */
    public function all(): array
    {
        return $this->providers;
    }
}
