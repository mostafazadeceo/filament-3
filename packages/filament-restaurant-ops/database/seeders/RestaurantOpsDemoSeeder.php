<?php

namespace Haida\FilamentRestaurantOps\Database\Seeders;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentRestaurantOps\Models\RestaurantGoodsReceipt;
use Haida\FilamentRestaurantOps\Models\RestaurantItem;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuItem;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuSale;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuSaleLine;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseOrder;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseOrderLine;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseRequest;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseRequestLine;
use Haida\FilamentRestaurantOps\Models\RestaurantRecipe;
use Haida\FilamentRestaurantOps\Models\RestaurantRecipeLine;
use Haida\FilamentRestaurantOps\Models\RestaurantSupplier;
use Haida\FilamentRestaurantOps\Models\RestaurantUom;
use Haida\FilamentRestaurantOps\Models\RestaurantWarehouse;
use Haida\FilamentRestaurantOps\Services\RestaurantGoodsReceiptService;
use Haida\FilamentRestaurantOps\Services\RestaurantMenuSaleService;
use Illuminate\Database\Seeder;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class RestaurantOpsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()->first()
            ?? Tenant::query()->create([
                'name' => 'Demo Tenant',
                'slug' => 'demo-tenant',
            ]);

        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->first()
            ?? AccountingCompany::query()->create([
                'name' => 'Demo Company',
                'timezone' => 'Asia/Tehran',
                'base_currency' => 'IRR',
            ]);

        $branch = AccountingBranch::query()->first()
            ?? AccountingBranch::query()->create([
                'company_id' => $company->getKey(),
                'name' => 'Main Branch',
            ]);

        $warehouse = RestaurantWarehouse::query()->first()
            ?? RestaurantWarehouse::query()->create([
                'company_id' => $company->getKey(),
                'branch_id' => $branch->getKey(),
                'name' => 'Main Warehouse',
                'type' => 'main',
            ]);

        $uom = RestaurantUom::query()->first()
            ?? RestaurantUom::query()->create([
                'company_id' => $company->getKey(),
                'name' => 'kg',
                'symbol' => 'kg',
                'is_base' => true,
            ]);

        $rice = RestaurantItem::query()->firstOrCreate(
            ['company_id' => $company->getKey(), 'name' => 'Rice'],
            [
                'base_uom_id' => $uom->getKey(),
                'purchase_uom_id' => $uom->getKey(),
                'consumption_uom_id' => $uom->getKey(),
                'track_batch' => false,
                'track_expiry' => false,
            ]
        );

        $chicken = RestaurantItem::query()->firstOrCreate(
            ['company_id' => $company->getKey(), 'name' => 'Chicken'],
            [
                'base_uom_id' => $uom->getKey(),
                'purchase_uom_id' => $uom->getKey(),
                'consumption_uom_id' => $uom->getKey(),
                'track_batch' => true,
                'track_expiry' => true,
            ]
        );

        $supplier = RestaurantSupplier::query()->firstOrCreate(
            ['company_id' => $company->getKey(), 'name' => 'Demo Supplier'],
            ['status' => 'active']
        );

        $purchaseRequest = RestaurantPurchaseRequest::query()->create([
            'company_id' => $company->getKey(),
            'branch_id' => $branch->getKey(),
            'status' => 'approved',
            'needed_at' => now()->addDay(),
        ]);

        RestaurantPurchaseRequestLine::query()->create([
            'purchase_request_id' => $purchaseRequest->getKey(),
            'item_id' => $rice->getKey(),
            'uom_id' => $uom->getKey(),
            'quantity' => 20,
        ]);

        $purchaseOrder = RestaurantPurchaseOrder::query()->create([
            'company_id' => $company->getKey(),
            'branch_id' => $branch->getKey(),
            'supplier_id' => $supplier->getKey(),
            'purchase_request_id' => $purchaseRequest->getKey(),
            'order_no' => 'PO-1001',
            'order_date' => now(),
            'status' => 'sent',
        ]);

        RestaurantPurchaseOrderLine::query()->create([
            'purchase_order_id' => $purchaseOrder->getKey(),
            'item_id' => $chicken->getKey(),
            'uom_id' => $uom->getKey(),
            'quantity' => 10,
            'unit_price' => 450000,
            'line_total' => 4500000,
        ]);

        RestaurantPurchaseOrderLine::query()->create([
            'purchase_order_id' => $purchaseOrder->getKey(),
            'item_id' => $rice->getKey(),
            'uom_id' => $uom->getKey(),
            'quantity' => 20,
            'unit_price' => 200000,
            'line_total' => 4000000,
        ]);

        $goodsReceipt = RestaurantGoodsReceipt::query()->create([
            'company_id' => $company->getKey(),
            'branch_id' => $branch->getKey(),
            'warehouse_id' => $warehouse->getKey(),
            'supplier_id' => $supplier->getKey(),
            'purchase_order_id' => $purchaseOrder->getKey(),
            'receipt_no' => 'GR-1001',
            'receipt_date' => now(),
            'status' => 'draft',
        ]);

        $goodsReceipt->lines()->create([
            'item_id' => $rice->getKey(),
            'uom_id' => $uom->getKey(),
            'quantity' => 30,
            'unit_cost' => 200000,
            'line_total' => 6000000,
        ]);

        $goodsReceipt->lines()->create([
            'item_id' => $chicken->getKey(),
            'uom_id' => $uom->getKey(),
            'quantity' => 20,
            'unit_cost' => 450000,
            'batch_no' => 'BATCH-CH-1',
            'expires_at' => now()->addDays(7),
            'line_total' => 9000000,
        ]);

        app(RestaurantGoodsReceiptService::class)->post($goodsReceipt);

        $recipe = RestaurantRecipe::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'Chicken Rice',
            'yield_quantity' => 1,
            'yield_uom_id' => $uom->getKey(),
            'waste_percent' => 2,
            'is_active' => true,
        ]);

        RestaurantRecipeLine::query()->create([
            'recipe_id' => $recipe->getKey(),
            'item_id' => $rice->getKey(),
            'uom_id' => $uom->getKey(),
            'quantity' => 0.3,
        ]);

        RestaurantRecipeLine::query()->create([
            'recipe_id' => $recipe->getKey(),
            'item_id' => $chicken->getKey(),
            'uom_id' => $uom->getKey(),
            'quantity' => 0.2,
        ]);

        $menuItem = RestaurantMenuItem::query()->create([
            'company_id' => $company->getKey(),
            'recipe_id' => $recipe->getKey(),
            'name' => 'Chicken Rice Plate',
            'price' => 1200000,
            'is_active' => true,
        ]);

        $menuSale = RestaurantMenuSale::query()->create([
            'company_id' => $company->getKey(),
            'branch_id' => $branch->getKey(),
            'warehouse_id' => $warehouse->getKey(),
            'sale_date' => now(),
            'source' => 'pos',
            'external_ref' => 'POS-9001',
            'status' => 'draft',
        ]);

        RestaurantMenuSaleLine::query()->create([
            'menu_sale_id' => $menuSale->getKey(),
            'menu_item_id' => $menuItem->getKey(),
            'quantity' => 5,
            'unit_price' => 1200000,
            'line_total' => 6000000,
        ]);

        app(RestaurantMenuSaleService::class)->post($menuSale);

        TenantContext::setTenant(null);
    }
}
