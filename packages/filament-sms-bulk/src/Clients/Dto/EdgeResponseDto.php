<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Clients\Dto;

final class EdgeResponseDto
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public readonly array $data,
        public readonly array $meta,
        public readonly int $statusCode,
        public readonly string $correlationId,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'meta' => $this->meta,
            'status_code' => $this->statusCode,
            'correlation_id' => $this->correlationId,
        ];
    }
}
