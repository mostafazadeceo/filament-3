<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Clients\Exceptions;

use RuntimeException;

class IppanelException extends RuntimeException
{
    /** @param array<string, mixed> $payload */
    public function __construct(
        string $message,
        protected ?int $statusCode = null,
        protected array $payload = [],
    ) {
        parent::__construct($message, $statusCode ?? 0);
    }

    public function statusCode(): ?int
    {
        return $this->statusCode;
    }

    /** @return array<string, mixed> */
    public function payload(): array
    {
        return $this->payload;
    }
}
