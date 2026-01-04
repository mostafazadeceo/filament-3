<?php

namespace Tests\Feature\CommerceCheckout;

use App\Models\User;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\WalletTransaction;
use Filamat\IamSuite\Services\WalletService;
use Filamat\IamSuite\Support\TenantContext;
use Haida\CommerceCatalog\Models\CatalogProduct;
use Haida\CommerceCheckout\Services\CartService;
use Haida\CommerceCheckout\Services\CheckoutService;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\InventoryDoc;
use Vendor\FilamentAccountingIr\Models\InventoryItem;
use Vendor\FilamentAccountingIr\Models\InventoryWarehouse;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_order_and_wallet_payment(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Checkout',
            'slug' => 'tenant-checkout',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $user = User::factory()->create([
            'email' => 'buyer@example.test',
        ]);

        $tenant->users()->attach($user->getKey(), [
            'role' => 'member',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $site = Site::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Shop',
            'slug' => 'shop',
            'type' => 'store',
            'status' => 'published',
            'currency' => 'IRR',
        ]);

        $company = AccountingCompany::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Commerce Co',
        ]);
        $warehouse = InventoryWarehouse::query()->create([
            'tenant_id' => $tenant->getKey(),
            'company_id' => $company->getKey(),
            'name' => 'Main',
            'is_active' => true,
        ]);
        $inventoryItem = InventoryItem::query()->create([
            'tenant_id' => $tenant->getKey(),
            'company_id' => $company->getKey(),
            'current_stock' => 10,
            'allow_negative' => false,
        ]);

        $product = CatalogProduct::query()->create([
            'tenant_id' => $tenant->getKey(),
            'site_id' => $site->getKey(),
            'name' => 'Coffee',
            'slug' => 'coffee',
            'type' => 'physical',
            'status' => 'published',
            'currency' => 'IRR',
            'price' => 100,
            'track_inventory' => true,
            'inventory_item_id' => $inventoryItem->getKey(),
        ]);

        $walletService = app(WalletService::class);
        $wallet = $walletService->createWallet($user, $tenant, 'IRR');
        $walletService->credit($wallet, 1000, 'seed-credit', ['source' => 'test']);

        $cartService = app(CartService::class);
        $cart = $cartService->getOrCreateCart($tenant->getKey(), $site, $user->getKey());
        $cartService->addItem($cart, $product, null, 2);

        $checkoutService = app(CheckoutService::class);
        $order = $checkoutService->checkout($cart, $user, [
            'idempotency_key' => 'checkout-1',
            'payment_method' => 'wallet',
            'customer_name' => 'Buyer',
        ]);

        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('processing', $order->status);
        $this->assertSame(200.0, (float) $order->total);
        $this->assertCount(1, $order->payments);

        $wallet->refresh();
        $this->assertSame(800.0, (float) $wallet->balance);

        $inventoryItem->refresh();
        $this->assertSame(8.0, (float) $inventoryItem->current_stock);
        $this->assertSame(1, InventoryDoc::query()->where('doc_type', 'issue')->count());

        $order2 = $checkoutService->checkout($cart, $user, [
            'idempotency_key' => 'checkout-1',
            'payment_method' => 'wallet',
        ]);

        $this->assertSame($order->getKey(), $order2->getKey());
        $this->assertSame(1, WalletTransaction::query()->where('idempotency_key', 'checkout-1:payment')->count());
    }
}
