<?php

namespace Haida\FilamentLoyaltyClub\Adapters;

use Carbon\CarbonImmutable;
use Haida\FilamentLoyaltyClub\Contracts\PurchaseAdapterInterface;
use Haida\FilamentLoyaltyClub\Support\PurchaseData;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuSale;

class RestaurantMenuSaleAdapter implements PurchaseAdapterInterface
{
    public function resolve(array $payload): PurchaseData
    {
        $saleId = $payload['menu_sale_id'] ?? $payload['sale_id'] ?? null;
        if ($saleId && class_exists(RestaurantMenuSale::class)) {
            $sale = RestaurantMenuSale::query()->find($saleId);
            if ($sale) {
                $occurredAt = $sale->sale_date ? CarbonImmutable::parse($sale->sale_date) : null;

                return new PurchaseData(
                    (float) $sale->total_amount,
                    (string) ($payload['currency'] ?? 'irr'),
                    (string) ($sale->external_ref ?? $sale->getKey()),
                    $occurredAt,
                    [
                        'menu_sale_id' => $sale->getKey(),
                        'status' => $sale->status,
                        'branch_id' => $sale->branch_id,
                        'warehouse_id' => $sale->warehouse_id,
                    ]
                );
            }
        }

        $fallback = new FallbackPurchaseAdapter;

        return $fallback->resolve($payload);
    }
}
