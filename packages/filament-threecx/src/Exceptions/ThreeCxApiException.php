<?php

namespace Haida\FilamentThreeCx\Exceptions;

use RuntimeException;

class ThreeCxApiException extends RuntimeException
{
    protected ?int $statusCode = null;

    protected ?array $payload = null;

    public static function fromResponse(?int $statusCode, ?array $payload = null, ?string $message = null): static
    {
        $message = $message ?: 'درخواست 3CX ناموفق بود.';
        $exception = new static($message, $statusCode ?? 0);
        $exception->statusCode = $statusCode;
        $exception->payload = $payload;

        return $exception;
    }

    public function statusCode(): ?int
    {
        return $this->statusCode;
    }

    public function payload(): ?array
    {
        return $this->payload;
    }
}
