<?php

namespace Haida\FilamentNotify\Core\Support\Sending;

class DeliveryResult
{
    public function __construct(
        public bool $success,
        public ?array $response = null,
        public ?string $error = null,
    ) {}

    public static function success(?array $response = null): self
    {
        return new self(true, $response, null);
    }

    public static function failure(string $error, ?array $response = null): self
    {
        return new self(false, $response, $error);
    }
}
