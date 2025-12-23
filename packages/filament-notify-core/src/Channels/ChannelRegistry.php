<?php

namespace Haida\FilamentNotify\Core\Channels;

use Haida\FilamentNotify\Core\Contracts\ChannelDriver;

class ChannelRegistry
{
    /**
     * @var array<string, ChannelDriver>
     */
    protected array $drivers = [];

    public function register(ChannelDriver $driver): void
    {
        $this->drivers[$driver->key()] = $driver;
    }

    /**
     * @return array<string, ChannelDriver>
     */
    public function all(): array
    {
        return $this->drivers;
    }

    /**
     * @return array<string, ChannelDriver>
     */
    public function installed(): array
    {
        return array_filter($this->drivers, static fn (ChannelDriver $driver): bool => $driver->isInstalled());
    }

    public function get(string $key): ?ChannelDriver
    {
        return $this->drivers[$key] ?? null;
    }
}
