<?php

namespace Haida\FilamentCommerceCore\Http\Controllers\Api\V1;

use Haida\FilamentCommerceCore\Models\CommerceBrand;
use Haida\FilamentCommerceCore\Models\CommerceCategory;
use Haida\FilamentCommerceCore\Models\CommerceProduct;
use Haida\FilamentCommerceCore\Models\CommerceVariant;
use Illuminate\Http\Request;

class CatalogSnapshotController extends ApiController
{
    public function index(Request $request): array
    {
        $since = $this->parseSince($request);

        $products = CommerceProduct::query()
            ->when($since, fn ($query) => $query->where('updated_at', '>', $since))
            ->with(['categories:id'])
            ->get();

        $variants = CommerceVariant::query()
            ->when($since, fn ($query) => $query->where('updated_at', '>', $since))
            ->get();

        $categories = CommerceCategory::query()
            ->when($since, fn ($query) => $query->where('updated_at', '>', $since))
            ->get();

        $brands = CommerceBrand::query()
            ->when($since, fn ($query) => $query->where('updated_at', '>', $since))
            ->get();

        return [
            'data' => [
                'products' => $products->map(function (CommerceProduct $product): array {
                    return [
                        'id' => $product->getKey(),
                        'site_id' => $product->site_id,
                        'brand_id' => $product->brand_id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'type' => $product->type,
                        'status' => $product->status,
                        'sku' => $product->sku,
                        'summary' => $product->summary,
                        'description' => $product->description,
                        'currency' => $product->currency,
                        'price' => $product->price,
                        'compare_at_price' => $product->compare_at_price,
                        'track_inventory' => (bool) $product->track_inventory,
                        'metadata' => $product->metadata,
                        'category_ids' => $product->categories->pluck('id')->values()->all(),
                        'updated_at' => optional($product->updated_at)->toIso8601String(),
                    ];
                })->values()->all(),
                'variants' => $variants->map(function (CommerceVariant $variant): array {
                    return [
                        'id' => $variant->getKey(),
                        'product_id' => $variant->product_id,
                        'name' => $variant->name,
                        'sku' => $variant->sku,
                        'barcode' => $variant->barcode,
                        'status' => $variant->status,
                        'currency' => $variant->currency,
                        'price' => $variant->price,
                        'compare_at_price' => $variant->compare_at_price,
                        'attributes' => $variant->attributes,
                        'metadata' => $variant->metadata,
                        'updated_at' => optional($variant->updated_at)->toIso8601String(),
                    ];
                })->values()->all(),
                'categories' => $categories->map(function (CommerceCategory $category): array {
                    return [
                        'id' => $category->getKey(),
                        'parent_id' => $category->parent_id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description,
                        'sort_order' => $category->sort_order,
                        'is_active' => (bool) $category->is_active,
                        'updated_at' => optional($category->updated_at)->toIso8601String(),
                    ];
                })->values()->all(),
                'brands' => $brands->map(function (CommerceBrand $brand): array {
                    return [
                        'id' => $brand->getKey(),
                        'name' => $brand->name,
                        'slug' => $brand->slug,
                        'description' => $brand->description,
                        'is_active' => (bool) $brand->is_active,
                        'updated_at' => optional($brand->updated_at)->toIso8601String(),
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
