<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Services\Campaign;

use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Services\ProviderClientFactory;

class CampaignPricingService
{
    public function __construct(protected ProviderClientFactory $clients) {}

    /**
     * @param array<string, mixed> $payload
     * @return array{estimate: float, pricing_snapshot: array<string, mixed>}
     */
    public function estimate(SmsBulkProviderConnection $connection, array $payload, int $recipientCount): array
    {
        try {
            $response = $this->clients->make($connection)->calculatePrice([
                'number' => $payload['sender'] ?? $connection->default_sender,
                'message' => $payload['message'] ?? '',
            ]);

            $data = (array) ($response['data'] ?? []);
            $unit = (float) ($data['other_price'] ?? $data['mci_price'] ?? 0);
            $parts = (int) ($data['parts'] ?? 1);

            return [
                'estimate' => $unit * $parts * max(1, $recipientCount),
                'pricing_snapshot' => $response,
            ];
        } catch (\Throwable $exception) {
            $parts = $this->estimateParts((string) ($payload['message'] ?? ''));
            $unit = 1.0;

            return [
                'estimate' => $unit * $parts * max(1, $recipientCount),
                'pricing_snapshot' => [
                    'fallback' => true,
                    'parts' => $parts,
                    'unit' => $unit,
                    'error' => $exception->getMessage(),
                ],
            ];
        }
    }

    protected function estimateParts(string $message): int
    {
        $length = mb_strlen($message);
        if ($length <= 70) {
            return 1;
        }

        return (int) ceil($length / 67);
    }
}
