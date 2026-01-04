#!/usr/bin/env bash
set -euo pipefail

if [[ "${DB_CONNECTION:-}" == "sqlite" && -n "${DB_DATABASE:-}" ]]; then
  mkdir -p "$(dirname "$DB_DATABASE")"
  touch "$DB_DATABASE"
fi

php artisan migrate --force

PHP_CODE=$(cat <<'PHP'
use App\Models\User;
use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Filamat\IamSuite\Services\WalletService;
use Haida\SiteBuilderCore\Models\Site;
use Haida\SiteBuilderCore\Services\SitePublisher;
use Haida\CommerceCatalog\Models\CatalogProduct;
use Haida\CommerceCheckout\Models\Cart;
use Haida\CommerceCheckout\Models\CartItem;
use Haida\CommerceCheckout\Services\CheckoutService;

$user = User::firstOrCreate([
    'email' => 'demo@local.test',
], [
    'name' => 'Demo User',
    'password' => bcrypt('secret'),
]);

$org = Organization::firstOrCreate([
    'name' => 'Demo Org',
], [
    'owner_user_id' => $user->id,
]);

$tenant = Tenant::firstOrCreate([
    'slug' => 'demo-tenant',
], [
    'name' => 'Demo Tenant',
    'organization_id' => $org->id,
    'owner_user_id' => $user->id,
]);

TenantContext::setTenant($tenant);

$site = Site::firstOrCreate([
    'tenant_id' => $tenant->id,
    'slug' => 'demo-site',
], [
    'name' => 'Demo Site',
    'type' => 'store',
    'status' => 'draft',
    'default_locale' => 'fa',
    'currency' => 'IRR',
    'timezone' => 'Asia/Tehran',
    'theme_key' => 'relograde-v1',
]);

app(SitePublisher::class)->publish($site, $user->id);

$product = CatalogProduct::firstOrCreate([
    'tenant_id' => $tenant->id,
    'site_id' => $site->id,
    'slug' => 'demo-product',
], [
    'name' => 'محصول دمو',
    'type' => 'physical',
    'status' => 'published',
    'currency' => 'IRR',
    'price' => 100000,
]);

$cart = Cart::query()->where('tenant_id', $tenant->id)->where('status', 'active')->first();
if (! $cart) {
    $cart = Cart::create([
        'tenant_id' => $tenant->id,
        'site_id' => $site->id,
        'user_id' => $user->id,
        'status' => 'active',
        'currency' => 'IRR',
        'subtotal' => 100000,
        'discount_total' => 0,
        'tax_total' => 0,
        'shipping_total' => 0,
        'total' => 100000,
    ]);

    CartItem::create([
        'tenant_id' => $tenant->id,
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'variant_id' => null,
        'name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 1,
        'currency' => 'IRR',
        'unit_price' => 100000,
        'line_total' => 100000,
    ]);
}

$walletService = app(WalletService::class);
$wallet = $walletService->createWallet($user, $tenant, 'IRR');
$walletService->credit($wallet, 200000, 'demo-credit', ['source' => 'demo']);

$order = app(CheckoutService::class)->checkout($cart, $user, [
    'payment_method' => 'wallet',
    'idempotency_key' => 'demo-checkout',
]);

echo "Order: {$order->number}\n";
PHP
)

php artisan tinker --execute "$PHP_CODE"

printf '\nDemo completed.\n'
