<?php

namespace Haida\FilamentRelograde\Services;

use Haida\FilamentRelograde\Clients\RelogradeClientFactory;
use Haida\FilamentRelograde\Models\RelogradeAccount;
use Haida\FilamentRelograde\Models\RelogradeBrand;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Models\RelogradeProduct;
use Throwable;

class RelogradeSyncService
{
    public function __construct(
        protected RelogradeClientFactory $clientFactory,
        protected RelogradeAuditLogger $auditLogger,
    ) {}

    public function syncBrands(RelogradeConnection $connection, bool $fullSync = true): int
    {
        if ($fullSync) {
            RelogradeBrand::query()->where('connection_id', $connection->getKey())->delete();
        }

        $client = $this->clientFactory->make($connection);
        $count = 0;

        foreach ($client->iterateBrands() as $brandData) {
            try {
                $brand = RelogradeBrand::updateOrCreate([
                    'connection_id' => $connection->getKey(),
                    'slug' => data_get($brandData, 'slug'),
                ], [
                    'brand_name' => data_get($brandData, 'brandName'),
                    'category' => data_get($brandData, 'category'),
                    'redeem_type' => data_get($brandData, 'redeemType'),
                    'raw_json' => $brandData,
                    'synced_at' => now(),
                ]);

                $brand->options()->delete();

                $options = data_get($brandData, 'options', []);
                foreach ($options as $optionData) {
                    $brand->options()->create([
                        'redeem_value' => data_get($optionData, 'redeemValue'),
                        'raw_json' => $optionData,
                    ]);
                }

                $count++;
            } catch (Throwable $exception) {
                $this->auditLogger->log('brands.sync_failed', $connection, [
                    'payload' => [
                        'slug' => data_get($brandData, 'slug'),
                        'error' => $exception->getMessage(),
                    ],
                ]);

                report($exception);
            }
        }

        $this->auditLogger->log('brands.sync', $connection, [
            'payload' => [
                'count' => $count,
                'full_sync' => $fullSync,
            ],
        ]);

        return $count;
    }

    public function syncProducts(RelogradeConnection $connection, bool $fullSync = true): int
    {
        if ($fullSync) {
            RelogradeProduct::query()->where('connection_id', $connection->getKey())->delete();
        }

        $client = $this->clientFactory->make($connection);
        $count = 0;

        foreach ($client->iterateProducts() as $productData) {
            try {
                RelogradeProduct::updateOrCreate([
                    'connection_id' => $connection->getKey(),
                    'slug' => data_get($productData, 'slug'),
                ], [
                    'name' => data_get($productData, 'name'),
                    'brand_slug' => data_get($productData, 'brandSlug'),
                    'brand_name' => data_get($productData, 'brandName'),
                    'category' => data_get($productData, 'category'),
                    'redeem_type' => data_get($productData, 'redeemType'),
                    'redeem_value' => data_get($productData, 'redeemValue'),
                    'is_stocked' => (bool) data_get($productData, 'isStocked', false),
                    'is_variable_product' => (bool) data_get($productData, 'isVariableProduct', false),
                    'face_value_currency' => data_get($productData, 'faceValueCurrency'),
                    'face_value_amount' => data_get($productData, 'faceValueAmount'),
                    'face_value_min' => data_get($productData, 'faceValueMin'),
                    'face_value_max' => data_get($productData, 'faceValueMax'),
                    'price_amount' => data_get($productData, 'priceAmount'),
                    'price_currency' => data_get($productData, 'priceCurrency'),
                    'fee_variable' => data_get($productData, 'feeVariable'),
                    'fee_fixed' => data_get($productData, 'feeFixed'),
                    'fee_currency' => data_get($productData, 'feeCurrency'),
                    'raw_json' => $productData,
                    'synced_at' => now(),
                ]);

                $count++;
            } catch (Throwable $exception) {
                $this->auditLogger->log('products.sync_failed', $connection, [
                    'payload' => [
                        'slug' => data_get($productData, 'slug'),
                        'error' => $exception->getMessage(),
                    ],
                ]);

                report($exception);
            }
        }

        $this->auditLogger->log('products.sync', $connection, [
            'payload' => [
                'count' => $count,
                'full_sync' => $fullSync,
            ],
        ]);

        return $count;
    }

    public function syncAccounts(RelogradeConnection $connection): int
    {
        $client = $this->clientFactory->make($connection);
        $accounts = $client->listAccounts();

        $count = 0;
        foreach ($accounts as $accountData) {
            RelogradeAccount::updateOrCreate([
                'connection_id' => $connection->getKey(),
                'currency' => data_get($accountData, 'currency'),
                'state' => data_get($accountData, 'state'),
            ], [
                'total_amount' => data_get($accountData, 'totalAmount', 0),
                'raw_json' => $accountData,
                'synced_at' => now(),
            ]);
            $count++;
        }

        $this->auditLogger->log('accounts.sync', $connection, [
            'payload' => [
                'count' => $count,
            ],
        ]);

        return $count;
    }
}
