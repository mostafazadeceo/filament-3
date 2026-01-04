<?php

namespace Haida\FilamentCommerceCore\Http\Controllers\Api\V1;

use Haida\FilamentCommerceCore\Models\CommercePrice;
use Haida\FilamentCommerceCore\Models\CommercePriceList;
use Illuminate\Http\Request;

class PricingSnapshotController extends ApiController
{
    public function index(Request $request): array
    {
        $since = $this->parseSince($request);

        $priceLists = CommercePriceList::query()
            ->when($since, fn ($query) => $query->where('updated_at', '>', $since))
            ->get();

        $prices = CommercePrice::query()
            ->when($since, fn ($query) => $query->where('updated_at', '>', $since))
            ->get();

        return [
            'data' => [
                'price_lists' => $priceLists->map(function (CommercePriceList $list): array {
                    return [
                        'id' => $list->getKey(),
                        'name' => $list->name,
                        'code' => $list->code,
                        'currency' => $list->currency,
                        'status' => $list->status,
                        'starts_at' => optional($list->starts_at)->toIso8601String(),
                        'ends_at' => optional($list->ends_at)->toIso8601String(),
                        'metadata' => $list->metadata,
                        'updated_at' => optional($list->updated_at)->toIso8601String(),
                    ];
                })->values()->all(),
                'prices' => $prices->map(function (CommercePrice $price): array {
                    return [
                        'id' => $price->getKey(),
                        'price_list_id' => $price->price_list_id,
                        'product_id' => $price->product_id,
                        'variant_id' => $price->variant_id,
                        'currency' => $price->currency,
                        'price' => $price->price,
                        'compare_at_price' => $price->compare_at_price,
                        'starts_at' => optional($price->starts_at)->toIso8601String(),
                        'ends_at' => optional($price->ends_at)->toIso8601String(),
                        'metadata' => $price->metadata,
                        'updated_at' => optional($price->updated_at)->toIso8601String(),
                    ];
                })->values()->all(),
            ],
            'meta' => [
                'as_of' => now()->toIso8601String(),
                'since' => $since?->toIso8601String(),
            ],
        ];
    }
}
