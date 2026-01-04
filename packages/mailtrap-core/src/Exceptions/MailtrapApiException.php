<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Exceptions;

use Exception;

class MailtrapApiException extends Exception
{
    public function __construct(
        protected ?int $statusCode,
        protected ?array $payload,
        string $message,
    ) {
        parent::__construct($message);
    }

    public static function fromResponse(?int $statusCode, ?array $payload, ?string $fallback = null): self
    {
        $message = $fallback ?: ($payload['message'] ?? $payload['error'] ?? 'خطای نامشخص از Mailtrap');

        return new self($statusCode, $payload, $message);
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
