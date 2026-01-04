<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

use Filamat\IamSuite\Contracts\IamEvent;

abstract class SimpleIamEvent implements IamEvent
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(protected ?int $tenantId, protected array $payload = []) {}

    abstract public static function name(): string;

    public function eventName(): string
    {
        return static::name();
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function tenantId(): ?int
    {
        return $this->tenantId;
    }
}
