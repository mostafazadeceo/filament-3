<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Services;

use Haida\ProvidersEsimGoCore\Clients\EsimGoClientFactory;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Haida\ProvidersEsimGoCore\Models\EsimGoInventoryUsage;
use Illuminate\Support\Arr;

class EsimGoInventoryService
{
    public function __construct(
        protected EsimGoClientFactory $clientFactory,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function sync(EsimGoConnection $connection, array $filters = [], bool $sandbox = false): int
    {
        $client = $this->clientFactory->make($connection, $sandbox);
        $response = $client->listInventory($filters);

        $items = $response['data'] ?? $response['items'] ?? $response;
        if (! is_array($items)) {
            return 0;
        }

        $count = 0;
        foreach ($items as $item) {
            $usageId = data_get($item, 'usageId', data_get($item, 'usage_id', data_get($item, 'id')));
            if (! $usageId) {
                continue;
            }

            EsimGoInventoryUsage::query()->updateOrCreate([
                'tenant_id' => $connection->tenant_id,
                'usage_id' => (string) $usageId,
            ], [
                'bundle_name' => data_get($item, 'bundleName', data_get($item, 'bundle')),
                'remaining' => data_get($item, 'remaining', data_get($item, 'remainingQuantity')),
                'expiry_at' => data_get($item, 'expiry', data_get($item, 'expiryAt', data_get($item, 'expiryDate'))),
                'countries' => Arr::wrap(data_get($item, 'countries')),
                'fetched_at' => now(),
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function refund(EsimGoConnection $connection, array $payload, bool $sandbox = false): array
    {
        if (! (bool) config('providers-esim-go-core.inventory.refund_enabled', false)) {
            return [
                'success' => false,
                'message' => 'بازپرداخت اینونتوری غیرفعال است.',
            ];
        }

        $client = $this->clientFactory->make($connection, $sandbox);
        $response = $client->refundInventory($payload);

        return [
            'success' => true,
            'response' => $response,
        ];
    }
}
