<?php

namespace Tests\Feature\AccountingIr;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\AccountingCompanySetting;
use Vendor\FilamentAccountingIr\Models\InventoryDoc;
use Vendor\FilamentAccountingIr\Models\InventoryItem;
use Vendor\FilamentAccountingIr\Models\InventoryWarehouse;
use Vendor\FilamentAccountingIr\Models\StockMove;
use Vendor\FilamentAccountingIr\Services\InventoryDocService;

class InventoryDocPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_posting_receipt_updates_stock_and_creates_moves(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant A',
            'slug' => 'tenant-a',
        ]);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create(['name' => 'Alpha']);
        $warehouse = InventoryWarehouse::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'Main',
        ]);
        $item = InventoryItem::query()->create([
            'company_id' => $company->getKey(),
            'current_stock' => 0,
            'allow_negative' => false,
        ]);

        $doc = InventoryDoc::query()->create([
            'company_id' => $company->getKey(),
            'warehouse_id' => $warehouse->getKey(),
            'doc_type' => 'receipt',
            'doc_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $doc->lines()->create([
            'inventory_item_id' => $item->getKey(),
            'quantity' => 5,
            'unit_cost' => 100,
        ]);

        $posted = app(InventoryDocService::class)->post($doc);

        $item->refresh();

        $this->assertSame('posted', $posted->status);
        $this->assertSame(5.0, (float) $item->current_stock);
        $this->assertDatabaseCount('accounting_ir_stock_moves', 1);

        $move = StockMove::query()->first();
        $this->assertSame('in', $move->direction);
        $this->assertSame(5.0, (float) $move->quantity);
    }

    public function test_posting_issue_prevents_negative_stock(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant B',
            'slug' => 'tenant-b',
        ]);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create(['name' => 'Beta']);
        $warehouse = InventoryWarehouse::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'Main',
        ]);
        $item = InventoryItem::query()->create([
            'company_id' => $company->getKey(),
            'current_stock' => 0,
            'allow_negative' => false,
        ]);

        $doc = InventoryDoc::query()->create([
            'company_id' => $company->getKey(),
            'warehouse_id' => $warehouse->getKey(),
            'doc_type' => 'issue',
            'doc_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $doc->lines()->create([
            'inventory_item_id' => $item->getKey(),
            'quantity' => 2,
            'unit_cost' => 100,
        ]);

        $this->expectException(ValidationException::class);

        app(InventoryDocService::class)->post($doc);
    }

    public function test_company_setting_allows_negative_inventory(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant C',
            'slug' => 'tenant-c',
        ]);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create(['name' => 'Gamma']);
        AccountingCompanySetting::query()->create([
            'company_id' => $company->getKey(),
            'allow_negative_inventory' => true,
        ]);

        $warehouse = InventoryWarehouse::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'Main',
        ]);
        $item = InventoryItem::query()->create([
            'company_id' => $company->getKey(),
            'current_stock' => 0,
            'allow_negative' => false,
        ]);

        $doc = InventoryDoc::query()->create([
            'company_id' => $company->getKey(),
            'warehouse_id' => $warehouse->getKey(),
            'doc_type' => 'issue',
            'doc_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $doc->lines()->create([
            'inventory_item_id' => $item->getKey(),
            'quantity' => 2,
            'unit_cost' => 100,
        ]);

        $posted = app(InventoryDocService::class)->post($doc);

        $item->refresh();

        $this->assertSame('posted', $posted->status);
        $this->assertSame(-2.0, (float) $item->current_stock);
    }
}
