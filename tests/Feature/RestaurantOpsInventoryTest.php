<?php

namespace Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentRestaurantOps\Models\RestaurantGoodsReceipt;
use Haida\FilamentRestaurantOps\Models\RestaurantInventoryBalance;
use Haida\FilamentRestaurantOps\Models\RestaurantInventoryDoc;
use Haida\FilamentRestaurantOps\Models\RestaurantInventoryLot;
use Haida\FilamentRestaurantOps\Models\RestaurantItem;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuItem;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuSale;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuSaleLine;
use Haida\FilamentRestaurantOps\Models\RestaurantRecipe;
use Haida\FilamentRestaurantOps\Models\RestaurantRecipeLine;
use Haida\FilamentRestaurantOps\Models\RestaurantUom;
use Haida\FilamentRestaurantOps\Models\RestaurantWarehouse;
use Haida\FilamentRestaurantOps\Services\RestaurantGoodsReceiptService;
use Haida\FilamentRestaurantOps\Services\RestaurantInventoryDocService;
use Haida\FilamentRestaurantOps\Services\RestaurantMenuSaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class RestaurantOpsInventoryTest extends \Tests\TestCase
{
    use RefreshDatabase;

    public function test_goods_receipt_post_updates_balance_and_lot(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant',
        ]);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create([
            'name' => 'Test Company',
        ]);

        $warehouse = RestaurantWarehouse::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'انبار مرکزی',
        ]);

        $uom = RestaurantUom::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'کیلو',
        ]);

        $item = RestaurantItem::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'مرغ',
            'base_uom_id' => $uom->getKey(),
            'purchase_uom_id' => $uom->getKey(),
            'track_batch' => true,
            'track_expiry' => true,
        ]);

        $receipt = RestaurantGoodsReceipt::query()->create([
            'company_id' => $company->getKey(),
            'warehouse_id' => $warehouse->getKey(),
            'receipt_date' => now(),
            'status' => 'draft',
        ]);

        $receipt->lines()->create([
            'item_id' => $item->getKey(),
            'uom_id' => $uom->getKey(),
            'quantity' => 5,
            'unit_cost' => 100,
            'batch_no' => 'BATCH-1',
            'expires_at' => now()->addDays(10),
        ]);

        app(RestaurantGoodsReceiptService::class)->post($receipt);

        $balance = RestaurantInventoryBalance::query()
            ->where('warehouse_id', $warehouse->getKey())
            ->where('item_id', $item->getKey())
            ->first();

        $this->assertNotNull($balance);
        $this->assertSame(5.0, (float) $balance->quantity);

        $lot = RestaurantInventoryLot::query()
            ->where('warehouse_id', $warehouse->getKey())
            ->where('item_id', $item->getKey())
            ->first();

        $this->assertNotNull($lot);
        $this->assertSame(5.0, (float) $lot->quantity);
    }

    public function test_menu_sale_post_consumes_inventory(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Test Tenant 2',
            'slug' => 'test-tenant-2',
        ]);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create([
            'name' => 'Test Company 2',
        ]);

        $warehouse = RestaurantWarehouse::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'انبار آشپزخانه',
        ]);

        $uom = RestaurantUom::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'کیلو',
        ]);

        $item = RestaurantItem::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'برنج',
            'base_uom_id' => $uom->getKey(),
            'consumption_uom_id' => $uom->getKey(),
        ]);

        $inventoryDoc = RestaurantInventoryDoc::query()->create([
            'company_id' => $company->getKey(),
            'warehouse_id' => $warehouse->getKey(),
            'doc_type' => 'receipt',
            'doc_date' => now(),
            'status' => 'draft',
        ]);

        $inventoryDoc->lines()->create([
            'item_id' => $item->getKey(),
            'uom_id' => $uom->getKey(),
            'quantity' => 10,
            'unit_cost' => 50,
        ]);

        app(RestaurantInventoryDocService::class)->post($inventoryDoc);

        $recipe = RestaurantRecipe::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'پلو',
            'yield_quantity' => 1,
            'yield_uom_id' => $uom->getKey(),
        ]);

        RestaurantRecipeLine::query()->create([
            'recipe_id' => $recipe->getKey(),
            'item_id' => $item->getKey(),
            'uom_id' => $uom->getKey(),
            'quantity' => 0.5,
        ]);

        $menuItem = RestaurantMenuItem::query()->create([
            'company_id' => $company->getKey(),
            'recipe_id' => $recipe->getKey(),
            'name' => 'چلو',
            'price' => 200,
        ]);

        $menuSale = RestaurantMenuSale::query()->create([
            'company_id' => $company->getKey(),
            'warehouse_id' => $warehouse->getKey(),
            'sale_date' => now(),
            'status' => 'draft',
        ]);

        RestaurantMenuSaleLine::query()->create([
            'menu_sale_id' => $menuSale->getKey(),
            'menu_item_id' => $menuItem->getKey(),
            'quantity' => 2,
            'unit_price' => 200,
            'line_total' => 400,
        ]);

        app(RestaurantMenuSaleService::class)->post($menuSale);

        $balance = RestaurantInventoryBalance::query()
            ->where('warehouse_id', $warehouse->getKey())
            ->where('item_id', $item->getKey())
            ->first();

        $this->assertNotNull($balance);
        $this->assertSame(9.0, (float) $balance->quantity);
    }

    public function test_menu_sale_post_consumes_tracked_item_lot(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tracked Tenant',
            'slug' => 'tracked-tenant',
        ]);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create([
            'name' => 'Tracked Company',
        ]);

        $warehouse = RestaurantWarehouse::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'انبار اصلی',
        ]);

        $uom = RestaurantUom::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'کیلو',
        ]);

        $item = RestaurantItem::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'مرغ',
            'base_uom_id' => $uom->getKey(),
            'purchase_uom_id' => $uom->getKey(),
            'consumption_uom_id' => $uom->getKey(),
            'track_batch' => true,
            'track_expiry' => true,
        ]);

        $inventoryDoc = RestaurantInventoryDoc::query()->create([
            'company_id' => $company->getKey(),
            'warehouse_id' => $warehouse->getKey(),
            'doc_type' => 'receipt',
            'doc_date' => now(),
            'status' => 'draft',
        ]);

        $inventoryDoc->lines()->create([
            'item_id' => $item->getKey(),
            'uom_id' => $uom->getKey(),
            'quantity' => 5,
            'unit_cost' => 120,
            'batch_no' => 'LOT-1',
            'expires_at' => now()->addDays(5),
        ]);

        app(RestaurantInventoryDocService::class)->post($inventoryDoc);

        $recipe = RestaurantRecipe::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'خوراک مرغ',
            'yield_quantity' => 1,
            'yield_uom_id' => $uom->getKey(),
        ]);

        RestaurantRecipeLine::query()->create([
            'recipe_id' => $recipe->getKey(),
            'item_id' => $item->getKey(),
            'uom_id' => $uom->getKey(),
            'quantity' => 1,
        ]);

        $menuItem = RestaurantMenuItem::query()->create([
            'company_id' => $company->getKey(),
            'recipe_id' => $recipe->getKey(),
            'name' => 'خوراک مرغ ویژه',
            'price' => 500,
        ]);

        $menuSale = RestaurantMenuSale::query()->create([
            'company_id' => $company->getKey(),
            'warehouse_id' => $warehouse->getKey(),
            'sale_date' => now(),
            'status' => 'draft',
        ]);

        RestaurantMenuSaleLine::query()->create([
            'menu_sale_id' => $menuSale->getKey(),
            'menu_item_id' => $menuItem->getKey(),
            'quantity' => 1,
            'unit_price' => 500,
            'line_total' => 500,
        ]);

        app(RestaurantMenuSaleService::class)->post($menuSale);

        $lot = RestaurantInventoryLot::query()
            ->where('warehouse_id', $warehouse->getKey())
            ->where('item_id', $item->getKey())
            ->first();

        $this->assertNotNull($lot);
        $this->assertSame(4.0, (float) $lot->quantity);
    }
}
