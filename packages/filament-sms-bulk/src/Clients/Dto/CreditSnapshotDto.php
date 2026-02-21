<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Clients\Dto;

final class CreditSnapshotDto
{
    public function __construct(
        public readonly ?float $credit,
        public readonly string $currency,
    ) {}

    /** @param array<string, mixed> $payload */
    public static function fromPayload(array $payload): self
    {
        $data = (array) ($payload['data'] ?? []);

        return new self(
            isset($data['credit']) ? (float) $data['credit'] : null,
            (string) ($data['currency'] ?? 'IRR'),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'credit' => $this->credit,
            'currency' => $this->currency,
        ];
    }
}
