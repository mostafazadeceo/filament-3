<?php

namespace Haida\FilamentRelograde\Exceptions;

use RuntimeException;

class RelogradeApiException extends RuntimeException
{
    protected ?int $statusCode = null;

    protected ?array $payload = null;

    public static function fromResponse(?int $statusCode, ?array $payload = null, ?string $message = null): self
    {
        $message = $message ?: RelogradeErrorMapper::messageForStatus($statusCode);
        $exception = new self($message, $statusCode ?? 0);
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
