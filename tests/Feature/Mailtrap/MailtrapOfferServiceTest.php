<?php

declare(strict_types=1);

namespace Tests\Feature\Mailtrap;

use Filamat\IamSuite\Models\Tenant;
use Haida\CommerceOrders\Models\Order;
use Haida\CommerceOrders\Models\OrderItem;
use Haida\FeatureGates\Models\TenantFeatureOverride;
use Haida\MailtrapCore\Models\MailtrapOffer;
use Haida\MailtrapCore\Services\MailtrapOfferService;
use Haida\SiteBuilderCore\Enums\SiteStatus;
use Haida\SiteBuilderCore\Enums\SiteType;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailtrapOfferServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_offer_creates_catalog_product(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant E',
            'slug' => 'tenant-e',
            'status' => 'active',
        ]);

        $site = Site::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Store',
            'slug' => 'store',
            'type' => SiteType::Store->value,
            'status' => SiteStatus::Published->value,
            'currency' => 'USD',
        ]);

        $offer = MailtrapOffer::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Mailtrap Starter',
            'status' => 'active',
            'duration_days' => 30,
            'feature_keys' => ['mailtrap.connection.view'],
            'price' => 10,
            'currency' => 'USD',
        ]);

        $product = app(MailtrapOfferService::class)->publishToCatalog($offer, $site);

        $this->assertNotNull($product);
        $this->assertDatabaseHas('commerce_catalog_products', [
            'tenant_id' => $tenant->getKey(),
            'name' => 'Mailtrap Starter',
        ]);

        $this->assertSame($product->getKey(), $offer->refresh()->catalog_product_id);
    }

    public function test_grant_entitlements_from_order(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant F',
            'slug' => 'tenant-f',
            'status' => 'active',
        ]);

        $site = Site::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Store',
            'slug' => 'store-f',
            'type' => SiteType::Store->value,
            'status' => SiteStatus::Published->value,
            'currency' => 'USD',
        ]);

        $offer = MailtrapOffer::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Mailtrap Pro',
            'status' => 'active',
            'duration_days' => 30,
            'feature_keys' => ['mailtrap.connection.view', 'mailtrap.inbox.view'],
            'price' => 20,
            'currency' => 'USD',
        ]);

        $product = app(MailtrapOfferService::class)->publishToCatalog($offer, $site);

        $order = Order::query()->create([
            'tenant_id' => $tenant->getKey(),
            'site_id' => $site->getKey(),
            'status' => 'paid',
            'payment_status' => 'paid',
            'currency' => 'USD',
            'total' => 20,
        ]);

        OrderItem::query()->create([
            'tenant_id' => $tenant->getKey(),
            'order_id' => $order->getKey(),
            'product_id' => $product?->getKey(),
            'name' => $offer->name,
            'quantity' => 1,
            'currency' => 'USD',
            'unit_price' => 20,
            'line_total' => 20,
        ]);

        $count = app(MailtrapOfferService::class)->grantEntitlementsFromOrder($order);

        $this->assertSame(2, $count);
        $this->assertDatabaseHas((new TenantFeatureOverride)->getTable(), [
            'tenant_id' => $tenant->getKey(),
            'feature_key' => 'mailtrap.connection.view',
        ]);
    }
}
