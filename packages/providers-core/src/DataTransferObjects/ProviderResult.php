<?php

declare(strict_types=1);

namespace Haida\ProvidersCore\DataTransferObjects;

final class ProviderResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message = '',
        public readonly array $data = [],
    ) {}

    public static function ok(array $data = [], string $message = ''): self
    {
        return new self(true, $message, $data);
    }

    public static function fail(string $message, array $data = []): self
    {
        return new self(false, $message, $data);
    }
}
