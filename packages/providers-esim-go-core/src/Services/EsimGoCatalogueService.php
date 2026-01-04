<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Services;

use Haida\ProvidersEsimGoCore\Clients\EsimGoClientFactory;
use Haida\ProvidersEsimGoCore\Events\EsimGoCatalogueSynced;
use Haida\ProvidersEsimGoCore\Models\EsimGoCatalogueSnapshot;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Haida\ProvidersEsimGoCore\Models\EsimGoProduct;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class EsimGoCatalogueService
{
    public function __construct(
        protected EsimGoClientFactory $clientFactory,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function sync(EsimGoConnection $connection, array $filters = [], bool $force = false, bool $sandbox = false): int
    {
        $latest = EsimGoCatalogueSnapshot::query()
            ->where('tenant_id', $connection->tenant_id)
            ->latest('fetched_at')
            ->first();

        $cacheSeconds = (int) config('providers-esim-go-core.catalogue.cache_seconds', 3600);
        if (! $force && $latest && $latest->fetched_at instanceof Carbon) {
            if ($latest->fetched_at->diffInSeconds(now()) < $cacheSeconds) {
                return 0;
            }
        }

        $client = $this->clientFactory->make($connection, $sandbox);
        $items = $client->paginateCatalogue($filters)->values()->all();

        $hash = hash('sha256', json_encode($items));
        if (! $force && $latest && $latest->hash === $hash) {
            return 0;
        }

        EsimGoCatalogueSnapshot::query()->create([
            'tenant_id' => $connection->tenant_id,
            'fetched_at' => now(),
            'filters' => $filters,
            'hash' => $hash,
            'payload' => [
                'count' => count($items),
                'items' => $items,
            ],
            'source_version' => 'v2.5',
        ]);

        $count = 0;
        foreach ($items as $item) {
            $bundleName = (string) data_get($item, 'name');
            if ($bundleName === '') {
                $bundleName = (string) data_get($item, 'bundleName', '');
            }

            if ($bundleName === '') {
                continue;
            }

            $payload = $this->mapProductPayload($connection->tenant_id, $item, $bundleName);

            EsimGoProduct::query()->updateOrCreate([
                'tenant_id' => $connection->tenant_id,
                'bundle_name' => $bundleName,
            ], $payload);

            $count++;
        }

        if ($count > 0) {
            event(new EsimGoCatalogueSynced($connection, $count));
        }

        return $count;
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    protected function mapProductPayload(int $tenantId, array $item, string $bundleName): array
    {
        $price = data_get($item, 'price');
        $currency = data_get($item, 'currency', 'USD');

        if (is_array($price)) {
            $currency = data_get($price, 'currency', $currency);
            $price = data_get($price, 'amount');
        }

        $dataAmount = data_get($item, 'dataAmount', data_get($item, 'dataAmountMb'));
        $duration = data_get($item, 'duration', data_get($item, 'durationDays'));

        $countriesRaw = Arr::wrap(data_get($item, 'countries'));
        $countries = [];
        $regions = [];
        $countriesMeta = [];

        foreach ($countriesRaw as $country) {
            if (is_array($country)) {
                $name = $country['name'] ?? $country['iso'] ?? null;
                if ($name) {
                    $countries[] = $name;
                }
                if (! empty($country['region'])) {
                    $regions[] = $country['region'];
                }
                $countriesMeta[] = [
                    'name' => $country['name'] ?? null,
                    'iso' => $country['iso'] ?? null,
                    'region' => $country['region'] ?? null,
                ];
            } elseif (is_string($country) && $country !== '') {
                $countries[] = $country;
                $countriesMeta[] = [
                    'name' => $country,
                    'iso' => null,
                    'region' => null,
                ];
            }
        }

        $countries = array_values(array_unique(array_filter($countries)));
        $regions = array_values(array_unique(array_filter(array_merge(
            $regions,
            Arr::wrap(data_get($item, 'region'))
        ))));

        return [
            'tenant_id' => $tenantId,
            'bundle_name' => $bundleName,
            'provider_product_id' => (string) data_get($item, 'id', $bundleName),
            'description' => data_get($item, 'description'),
            'groups' => Arr::wrap(data_get($item, 'groups')),
            'countries' => $countries,
            'countries_meta' => $countriesMeta,
            'region' => $regions,
            'allowances' => Arr::wrap(data_get($item, 'allowances')),
            'price' => is_numeric($price) ? (float) $price : 0,
            'currency' => (string) $currency,
            'data_amount_mb' => is_numeric($dataAmount) ? (int) $dataAmount : null,
            'duration_days' => is_numeric($duration) ? (int) $duration : null,
            'speed' => Arr::wrap(data_get($item, 'speed')),
            'autostart' => (bool) data_get($item, 'autostart', false),
            'unlimited' => (bool) data_get($item, 'unlimited', false),
            'roaming_enabled' => Arr::wrap(data_get($item, 'roamingEnabled')),
            'billing_type' => data_get($item, 'billingType'),
            'status' => data_get($item, 'status', 'active'),
        ];
    }
}
