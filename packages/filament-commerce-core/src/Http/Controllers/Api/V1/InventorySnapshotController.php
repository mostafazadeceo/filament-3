<?php

namespace Haida\FilamentCommerceCore\Http\Controllers\Api\V1;

use Haida\FilamentCommerceCore\Models\CommerceInventoryItem;
use Illuminate\Http\Request;

class InventorySnapshotController extends ApiController
{
    public function index(Request $request): array
    {
        $since = $this->parseSince($request);

        $items = CommerceInventoryItem::query()
            ->when($since, fn ($query) => $query->where('updated_at', '>', $since))
            ->get();

        return [
            'data' => [
                'inventory_items' => $items->map(function (CommerceInventoryItem $item): array {
                    return [
                        'id' => $item->getKey(),
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'sku' => $item->sku,
                        'location_label' => $item->location_label,
                        'quantity_on_hand' => $item->quantity_on_hand,
                        'quantity_reserved' => $item->quantity_reserved,
                        'status' => $item->status,
                        'metadata' => $item->metadata,
                        'updated_at' => optional($item->updated_at)->toIso8601String(),
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
