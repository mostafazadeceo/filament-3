<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCommerce\Services;

use Haida\CommerceCatalog\Models\CatalogProduct;
use Haida\CommerceCatalog\Models\CatalogVariant;
use Haida\CommerceCatalog\Services\CatalogPricingService;
use Haida\CommerceOrders\Models\Order;
use Haida\CommerceOrders\Models\OrderItem;
use Haida\ProvidersCore\DataTransferObjects\ProviderContext;
use Haida\ProvidersCore\Services\ProviderJobDispatcher;
use Haida\ProvidersCore\Support\ProviderAction;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Haida\ProvidersEsimGoCore\Models\EsimGoOrder;
use Haida\ProvidersEsimGoCore\Models\EsimGoProduct;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Support\Str;

class EsimGoCommerceService
{
    public function __construct(
        protected ProviderJobDispatcher $dispatcher,
        protected CatalogPricingService $pricingService,
    ) {}

    public function syncCatalogueToCommerce(EsimGoConnection $connection, ?Site $site = null): int
    {
        $site = $site ?: Site::query()->where('tenant_id', $connection->tenant_id)->orderBy('id')->first();
        if (! $site) {
            return 0;
        }

        $products = EsimGoProduct::query()->where('tenant_id', $connection->tenant_id)->get();
        $count = 0;

        foreach ($products as $product) {
            $catalogProduct = $this->upsertCatalogProduct($site, $product);
            $catalogVariant = $this->upsertCatalogVariant($catalogProduct, $product);

            $product->update([
                'catalog_product_id' => $catalogProduct->getKey(),
                'catalog_variant_id' => $catalogVariant->getKey(),
            ]);

            $count++;
        }

        return $count;
    }

    public function createProviderOrderForCommerceOrder(Order $order): ?int
    {
        $order->loadMissing('items');
        $esimItems = $this->resolveEsimItems($order);
        if ($esimItems === []) {
            return null;
        }

        $existing = EsimGoOrder::query()->where('tenant_id', $order->tenant_id)
            ->where('commerce_order_id', $order->getKey())
            ->latest('id')
            ->first();

        if ($existing) {
            return null;
        }

        $connection = EsimGoConnection::query()->where('tenant_id', $order->tenant_id)->default()->first();
        if (! $connection) {
            return null;
        }

        $payload = $this->buildProviderOrderPayload($order, $esimItems);

        $context = new ProviderContext($order->tenant_id, $connection->getKey(), (bool) ($connection->metadata['sandbox'] ?? false));
        $log = $this->dispatcher->dispatch(ProviderAction::CreateOrder, $context, 'esim-go', $payload);

        return $log->getKey();
    }

    public function applyFulfillment(EsimGoOrder $providerOrder): void
    {
        $commerceOrder = $providerOrder->commerceOrder;
        if (! $commerceOrder) {
            return;
        }

        $commerceOrder->loadMissing('items');
        $esims = $providerOrder->esims()->get();
        if ($esims->isEmpty()) {
            return;
        }

        $items = $this->resolveEsimItems($commerceOrder);
        if ($items === []) {
            return;
        }

        $index = 0;
        $assignedCount = 0;
        foreach ($items as $item) {
            $needed = max(1, (int) $item->quantity);
            $assigned = [];

            for ($i = 0; $i < $needed; $i++) {
                $esim = $esims[$index] ?? null;
                if (! $esim) {
                    break;
                }

                $assigned[] = [
                    'iccid' => $esim->iccid,
                    'matching_id' => $esim->matching_id,
                    'smdp_address' => $esim->smdp_address,
                    'state' => $esim->state,
                ];
                $index++;
                $assignedCount++;
            }

            if ($assigned !== []) {
                $meta = $item->meta ?? [];
                $meta['esim_go'] = array_merge($meta['esim_go'] ?? [], [
                    'esims' => $assigned,
                ]);

                $item->update(['meta' => $meta]);
            }
        }

        $allItemsCount = $commerceOrder->items->count();
        $esimItemsCount = count($items);
        $totalEsimQuantity = array_sum(array_map(fn (OrderItem $item) => (int) $item->quantity, $items));

        if (
            (bool) config('providers-esim-go-commerce.auto_fulfill', true)
            && $esimItemsCount === $allItemsCount
            && $assignedCount >= $totalEsimQuantity
        ) {
            $commerceOrder->update([
                'status' => 'fulfilled',
                'fulfilled_at' => now(),
            ]);
        }
    }

    /**
     * @return array<int, OrderItem>
     */
    protected function resolveEsimItems(Order $order): array
    {
        $items = [];
        foreach ($order->items as $item) {
            $esimProduct = EsimGoProduct::query()
                ->where('tenant_id', $order->tenant_id)
                ->where(function ($query) use ($item) {
                    $query->where('catalog_variant_id', $item->variant_id)
                        ->orWhere('catalog_product_id', $item->product_id);
                })
                ->first();

            if ($esimProduct) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param  array<int, OrderItem>  $items
     * @return array<string, mixed>
     */
    protected function buildProviderOrderPayload(Order $order, array $items): array
    {
        $orderItems = [];
        foreach ($items as $item) {
            $esimProduct = EsimGoProduct::query()
                ->where('tenant_id', $order->tenant_id)
                ->where(function ($query) use ($item) {
                    $query->where('catalog_variant_id', $item->variant_id)
                        ->orWhere('catalog_product_id', $item->product_id);
                })
                ->first();

            if (! $esimProduct) {
                continue;
            }

            $meta = $item->meta ?? [];
            $orderItems[] = array_filter([
                'type' => 'bundle',
                'quantity' => (int) $item->quantity,
                'item' => $esimProduct->bundle_name,
                'iccids' => data_get($meta, 'esim_go.iccids', []),
                'allowReassign' => (bool) data_get($meta, 'esim_go.allow_reassign', false),
                'profileID' => data_get($meta, 'esim_go.profile_id'),
            ], fn ($value) => $value !== null && $value !== []);
        }

        $assign = (bool) data_get($order->meta, 'esim_go.assign', true);
        $profileId = data_get($order->meta, 'esim_go.profile_id');

        return array_filter([
            'assign' => $assign,
            'profileID' => $profileId,
            'order' => $orderItems,
            'currency' => $order->currency,
            'total' => (float) $order->total,
            'reference' => $order->number,
            'commerce_order_id' => $order->getKey(),
        ], fn ($value) => $value !== null && $value !== []);
    }

    protected function upsertCatalogProduct(Site $site, EsimGoProduct $product): CatalogProduct
    {
        $slug = $this->generateSlug($site, $product->bundle_name, $product->catalog_product_id);
        $sku = 'ESIM-' . Str::upper(Str::slug($product->bundle_name, '-'));

        $price = (float) $product->price;
        $currency = (string) ($product->currency ?: $site->currency);
        $converted = $this->convertPriceToSiteCurrency($price, $currency, $site->currency);
        $fxMissing = false;
        if ($converted !== null) {
            $price = $converted;
            $currency = $site->currency;
        } elseif ((bool) config('providers-esim-go-commerce.force_site_currency', true)) {
            $currency = $site->currency;
            $fxMissing = true;
        }

        $meta = [
            'provider' => 'esim-go',
            'bundle_name' => $product->bundle_name,
            'countries' => $product->countries,
            'duration_days' => $product->duration_days,
            'data_amount_mb' => $product->data_amount_mb,
            'original_price' => (float) $product->price,
            'original_currency' => $product->currency ?: $site->currency,
            'fx_missing' => $fxMissing,
        ];

        $catalogProduct = CatalogProduct::query()->updateOrCreate([
            'tenant_id' => $site->tenant_id,
            'site_id' => $site->getKey(),
            'sku' => $sku,
        ], [
            'name' => $product->bundle_name,
            'slug' => $slug,
            'type' => 'digital_code',
            'status' => config('providers-esim-go-commerce.publish_status', 'published'),
            'summary' => $product->description,
            'description' => $product->description,
            'currency' => $currency,
            'price' => $price,
            'track_inventory' => false,
            'metadata' => $meta,
        ]);

        return $catalogProduct;
    }

    protected function upsertCatalogVariant(CatalogProduct $catalogProduct, EsimGoProduct $product): CatalogVariant
    {
        $sku = $catalogProduct->sku ?: ('ESIM-' . Str::upper(Str::slug($product->bundle_name, '-')));

        $price = (float) $product->price;
        $currency = (string) ($product->currency ?: $catalogProduct->currency);
        $converted = $this->convertPriceToSiteCurrency($price, $currency, $catalogProduct->currency);
        $fxMissing = false;
        if ($converted !== null) {
            $price = $converted;
            $currency = $catalogProduct->currency;
        } elseif ((bool) config('providers-esim-go-commerce.force_site_currency', true)) {
            $currency = $catalogProduct->currency;
            $fxMissing = true;
        }

        $meta = [
            'provider' => 'esim-go',
            'bundle_name' => $product->bundle_name,
            'countries' => $product->countries,
            'duration_days' => $product->duration_days,
            'data_amount_mb' => $product->data_amount_mb,
            'original_price' => (float) $product->price,
            'original_currency' => $product->currency ?: $catalogProduct->currency,
            'fx_missing' => $fxMissing,
        ];

        return CatalogVariant::query()->updateOrCreate([
            'tenant_id' => $catalogProduct->tenant_id,
            'product_id' => $catalogProduct->getKey(),
            'sku' => $sku,
        ], [
            'name' => $product->bundle_name,
            'currency' => $currency,
            'price' => $price,
            'is_default' => true,
            'attributes' => [
                'countries' => $product->countries,
                'duration_days' => $product->duration_days,
                'data_amount_mb' => $product->data_amount_mb,
            ],
            'metadata' => $meta,
        ]);
    }

    protected function convertPriceToSiteCurrency(float $amount, string $from, string $to): ?float
    {
        $converted = $this->pricingService->convert($amount, $from, $to);
        if ($converted === null) {
            return null;
        }

        return round($converted, 4);
    }

    protected function generateSlug(Site $site, string $name, ?int $currentId): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'esim-' . Str::lower(Str::random(6));
        }

        $slug = $base;
        $exists = CatalogProduct::query()
            ->where('site_id', $site->getKey())
            ->when($currentId, fn ($query) => $query->where('id', '!=', $currentId))
            ->where('slug', $slug)
            ->exists();

        if ($exists) {
            $slug = $base . '-' . substr(sha1($name), 0, 6);
        }

        return $slug;
    }
}
