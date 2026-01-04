# SPEC — filament-restaurant-ops

## معرفی
- پکیج: haida/filament-restaurant-ops
- توضیح: Restaurant procurement, inventory, and cost control module for Filament v4.
- Service Provider: Haida\FilamentRestaurantOps\FilamentRestaurantOpsServiceProvider
- Filament Plugin: Haida\FilamentRestaurantOps\FilamentRestaurantOpsPlugin (id: restaurant-ops)

## دامنه و قابلیت‌ها
- مدل‌ها:
- RestaurantGoodsReceipt.php
- RestaurantGoodsReceiptLine.php
- RestaurantInventoryBalance.php
- RestaurantInventoryDoc.php
- RestaurantInventoryDocLine.php
- RestaurantInventoryLot.php
- RestaurantItem.php
- RestaurantMenuItem.php
- RestaurantMenuSale.php
- RestaurantMenuSaleLine.php
- RestaurantPurchaseOrder.php
- RestaurantPurchaseOrderLine.php
- RestaurantPurchaseRequest.php
- RestaurantPurchaseRequestLine.php
- RestaurantRecipe.php
- RestaurantRecipeLine.php
- RestaurantStockMove.php
- RestaurantSupplier.php
- RestaurantUom.php
- RestaurantWarehouse.php
- منابع Filament:
- src/Filament/Resources/RestaurantGoodsReceiptResource.php
- src/Filament/Resources/RestaurantInventoryDocResource.php
- src/Filament/Resources/RestaurantItemResource.php
- src/Filament/Resources/RestaurantMenuItemResource.php
- src/Filament/Resources/RestaurantMenuSaleResource.php
- src/Filament/Resources/RestaurantPurchaseOrderResource.php
- src/Filament/Resources/RestaurantPurchaseRequestResource.php
- src/Filament/Resources/RestaurantRecipeResource.php
- src/Filament/Resources/RestaurantSupplierResource.php
- src/Filament/Resources/RestaurantUomResource.php
- src/Filament/Resources/RestaurantWarehouseResource.php
- کنترلرها/API:
- Api/V1/ApiController.php
- Api/V1/GoodsReceiptController.php
- Api/V1/InventoryDocController.php
- Api/V1/ItemController.php
- Api/V1/MenuItemController.php
- Api/V1/MenuSaleController.php
- Api/V1/OpenApiController.php
- Api/V1/PurchaseOrderController.php
- Api/V1/PurchaseRequestController.php
- Api/V1/RecipeController.php
- Api/V1/SupplierController.php
- Api/V1/UomController.php
- Api/V1/WarehouseController.php
- Jobs/Queue:
- ندارد
- Policyها:
- RestaurantGoodsReceiptPolicy.php
- RestaurantInventoryDocPolicy.php
- RestaurantItemPolicy.php
- RestaurantMenuItemPolicy.php
- RestaurantMenuSalePolicy.php
- RestaurantPurchaseOrderPolicy.php
- RestaurantPurchaseRequestPolicy.php
- RestaurantRecipePolicy.php
- RestaurantSupplierPolicy.php
- RestaurantUomPolicy.php
- RestaurantWarehousePolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): restaurant.goods_receipt.post, restaurant.inventory_doc.post, restaurant.menu_sale.post

## مدل داده
- Migrations:
- 2025_12_30_000001_create_restaurant_ops_core_tables.php
- 2025_12_30_000002_create_restaurant_ops_procurement_tables.php
- 2025_12_30_000003_create_restaurant_ops_inventory_tables.php
- 2025_12_30_000004_create_restaurant_ops_cost_tables.php
- 2025_12_30_000005_add_warehouse_to_menu_sales.php
- 2025_12_30_000006_add_accounting_links_to_restaurant_ops.php
- جدول‌ها:
- restaurant_goods_receipt_lines
- restaurant_goods_receipts
- restaurant_inventory_balances
- restaurant_inventory_doc_lines
- restaurant_inventory_docs
- restaurant_inventory_lots
- restaurant_items
- restaurant_menu_items
- restaurant_menu_sale_lines
- restaurant_menu_sales
- restaurant_purchase_order_lines
- restaurant_purchase_orders
- restaurant_purchase_request_lines
- restaurant_purchase_requests
- restaurant_recipe_lines
- restaurant_recipes
- restaurant_stock_moves
- restaurant_suppliers
- restaurant_uoms
- restaurant_warehouses
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/filament-restaurant-ops/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-restaurant-ops/config/filament-restaurant-ops.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
