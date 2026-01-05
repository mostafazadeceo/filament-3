<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Services;

use Haida\CommerceCatalog\Models\CatalogProduct;
use Haida\CommerceCatalog\Models\CatalogVariant;
use Haida\CommerceOrders\Models\Order;
use Haida\FeatureGates\Models\TenantFeatureOverride;
use Haida\MailtrapCore\Models\MailtrapOffer;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Support\Str;

class MailtrapOfferService
{
    public function __construct() {}

    public function publishToCatalog(MailtrapOffer $offer, ?Site $site = null): ?CatalogProduct
    {
        $site = $site ?: Site::query()->where('tenant_id', $offer->tenant_id)->orderBy('id')->first();
        if (! $site) {
            return null;
        }

        $slug = $offer->slug ?: Str::slug($offer->name);
        $slug = $slug ?: 'mailtrap-offer-'.$offer->getKey();

        $product = CatalogProduct::query()->updateOrCreate([
            'tenant_id' => $offer->tenant_id,
            'site_id' => $site->getKey(),
            'slug' => $slug,
        ], [
            'name' => $offer->name,
            'type' => 'service',
            'status' => $offer->status === 'active' ? 'published' : 'draft',
            'currency' => $offer->currency,
            'price' => $offer->price,
            'track_inventory' => false,
            'summary' => Str::limit((string) $offer->description, 140),
            'description' => $offer->description,
            'metadata' => [
                'mailtrap_offer_id' => $offer->getKey(),
                'feature_keys' => $offer->feature_keys,
                'duration_days' => $offer->duration_days,
            ],
            'published_at' => $offer->status === 'active' ? now() : null,
        ]);

        CatalogVariant::query()->updateOrCreate([
            'tenant_id' => $offer->tenant_id,
            'product_id' => $product->getKey(),
            'sku' => 'MAILTRAP-'.$offer->getKey(),
        ], [
            'name' => $offer->name,
            'currency' => $offer->currency,
            'price' => $offer->price,
            'is_default' => true,
            'metadata' => [
                'mailtrap_offer_id' => $offer->getKey(),
            ],
        ]);

        $offer->update([
            'slug' => $slug,
            'catalog_product_id' => $product->getKey(),
        ]);

        return $product;
    }

    public function grantEntitlementsFromOrder(Order $order): int
    {
        $order->loadMissing('items');
        $count = 0;

        foreach ($order->items as $item) {
            $offer = MailtrapOffer::query()
                ->where('tenant_id', $order->tenant_id)
                ->where('catalog_product_id', $item->product_id)
                ->first();

            if (! $offer || $offer->status !== 'active') {
                continue;
            }

            $count += $this->grantEntitlements($order->tenant_id, $offer);
        }

        return $count;
    }

    public function grantEntitlements(int $tenantId, MailtrapOffer $offer): int
    {
        $featureKeys = $offer->feature_keys ?: ['mailtrap.connection.view'];
        $durationDays = max(1, (int) $offer->duration_days);

        $applied = 0;
        foreach ($featureKeys as $featureKey) {
            $override = TenantFeatureOverride::query()
                ->where('tenant_id', $tenantId)
                ->where('feature_key', $featureKey)
                ->orderByDesc('ends_at')
                ->first();

            $startsAt = now();
            $endsAt = now()->addDays($durationDays);

            if ($override && $override->ends_at && $override->ends_at->isFuture()) {
                $startsAt = $override->ends_at;
                $endsAt = $override->ends_at->copy()->addDays($durationDays);
            }

            TenantFeatureOverride::query()->create([
                'tenant_id' => $tenantId,
                'feature_key' => $featureKey,
                'allowed' => true,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'limits' => $offer->limits,
            ]);

            $applied++;
        }

        return $applied;
    }
}
