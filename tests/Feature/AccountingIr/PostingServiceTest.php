<?php

namespace Tests\Feature\AccountingIr;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\AccountingCompanySetting;
use Vendor\FilamentAccountingIr\Models\AccountPlan;
use Vendor\FilamentAccountingIr\Models\AccountType;
use Vendor\FilamentAccountingIr\Models\ChartAccount;
use Vendor\FilamentAccountingIr\Models\FiscalYear;
use Vendor\FilamentAccountingIr\Models\JournalEntry;
use Vendor\FilamentAccountingIr\Models\Party;
use Vendor\FilamentAccountingIr\Models\PurchaseInvoice;
use Vendor\FilamentAccountingIr\Models\SalesInvoice;
use Vendor\FilamentAccountingIr\Services\PurchaseInvoiceService;
use Vendor\FilamentAccountingIr\Services\SalesInvoiceService;

class PostingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_posting_sales_invoice_creates_journal_entry(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant A',
            'slug' => 'tenant-a',
        ]);
        TenantContext::setTenant($tenant);

        [$company, $fiscalYear, $accounts] = $this->seedAccounts();
        $party = Party::query()->create([
            'company_id' => $company->getKey(),
            'party_type' => 'customer',
            'name' => 'Customer A',
        ]);

        config()->set('filament-accounting-ir.ledger.posting_requires_approval', false);
        config()->set('filament-accounting-ir.ledger.posting_accounts', [
            'sales_revenue' => $accounts['sales_revenue']->getKey(),
            'sales_tax' => $accounts['sales_tax']->getKey(),
            'accounts_receivable' => $accounts['accounts_receivable']->getKey(),
            'purchase_expense' => $accounts['purchase_expense']->getKey(),
            'purchase_tax' => $accounts['purchase_tax']->getKey(),
            'accounts_payable' => $accounts['accounts_payable']->getKey(),
            'cash' => null,
            'bank' => null,
        ]);

        $invoice = SalesInvoice::query()->create([
            'company_id' => $company->getKey(),
            'fiscal_year_id' => $fiscalYear->getKey(),
            'party_id' => $party->getKey(),
            'invoice_no' => 'SI-1001',
            'invoice_date' => now()->toDateString(),
            'status' => 'draft',
            'discount_total' => 0,
            'is_official' => false,
        ]);

        $invoice->lines()->create([
            'description' => 'Item A',
            'quantity' => 1,
            'unit_price' => 1000,
            'tax_rate' => 9,
            'tax_amount' => 90,
            'line_total' => 1000,
        ]);

        $issued = app(SalesInvoiceService::class)->issue($invoice);

        $entry = JournalEntry::query()
            ->where('source_type', 'sales_invoice')
            ->where('source_id', $issued->getKey())
            ->with('lines')
            ->first();

        $this->assertNotNull($entry);
        $this->assertSame('posted', $entry->status);

        $lines = $entry->lines->keyBy('account_id');
        $this->assertSame(1090.0, (float) $lines[$accounts['accounts_receivable']->getKey()]->debit);
        $this->assertSame(1000.0, (float) $lines[$accounts['sales_revenue']->getKey()]->credit);
        $this->assertSame(90.0, (float) $lines[$accounts['sales_tax']->getKey()]->credit);
    }

    public function test_posting_purchase_invoice_creates_journal_entry(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant B',
            'slug' => 'tenant-b',
        ]);
        TenantContext::setTenant($tenant);

        [$company, $fiscalYear, $accounts] = $this->seedAccounts();
        $party = Party::query()->create([
            'company_id' => $company->getKey(),
            'party_type' => 'supplier',
            'name' => 'Supplier A',
        ]);

        config()->set('filament-accounting-ir.ledger.posting_requires_approval', false);
        config()->set('filament-accounting-ir.ledger.posting_accounts', [
            'sales_revenue' => $accounts['sales_revenue']->getKey(),
            'sales_tax' => $accounts['sales_tax']->getKey(),
            'accounts_receivable' => $accounts['accounts_receivable']->getKey(),
            'purchase_expense' => $accounts['purchase_expense']->getKey(),
            'purchase_tax' => $accounts['purchase_tax']->getKey(),
            'accounts_payable' => $accounts['accounts_payable']->getKey(),
            'cash' => null,
            'bank' => null,
        ]);

        $invoice = PurchaseInvoice::query()->create([
            'company_id' => $company->getKey(),
            'fiscal_year_id' => $fiscalYear->getKey(),
            'party_id' => $party->getKey(),
            'invoice_no' => 'PI-2001',
            'invoice_date' => now()->toDateString(),
            'status' => 'draft',
            'discount_total' => 0,
        ]);

        $invoice->lines()->create([
            'description' => 'Item B',
            'quantity' => 1,
            'unit_price' => 500,
            'tax_rate' => 9,
            'tax_amount' => 45,
            'line_total' => 500,
        ]);

        $received = app(PurchaseInvoiceService::class)->receive($invoice);

        $entry = JournalEntry::query()
            ->where('source_type', 'purchase_invoice')
            ->where('source_id', $received->getKey())
            ->with('lines')
            ->first();

        $this->assertNotNull($entry);
        $this->assertSame('posted', $entry->status);

        $lines = $entry->lines->keyBy('account_id');
        $this->assertSame(500.0, (float) $lines[$accounts['purchase_expense']->getKey()]->debit);
        $this->assertSame(45.0, (float) $lines[$accounts['purchase_tax']->getKey()]->debit);
        $this->assertSame(545.0, (float) $lines[$accounts['accounts_payable']->getKey()]->credit);
    }

    public function test_company_settings_override_config_for_posting(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant C',
            'slug' => 'tenant-c',
        ]);
        TenantContext::setTenant($tenant);

        [$company, $fiscalYear, $accounts] = $this->seedAccounts();
        $party = Party::query()->create([
            'company_id' => $company->getKey(),
            'party_type' => 'customer',
            'name' => 'Customer B',
        ]);

        config()->set('filament-accounting-ir.ledger.posting_requires_approval', true);
        config()->set('filament-accounting-ir.ledger.posting_accounts', [
            'sales_revenue' => null,
            'sales_tax' => null,
            'accounts_receivable' => null,
            'purchase_expense' => null,
            'purchase_tax' => null,
            'accounts_payable' => null,
            'cash' => null,
            'bank' => null,
        ]);

        AccountingCompanySetting::query()->create([
            'company_id' => $company->getKey(),
            'posting_accounts' => [
                'sales_revenue' => $accounts['sales_revenue']->getKey(),
                'sales_tax' => $accounts['sales_tax']->getKey(),
                'accounts_receivable' => $accounts['accounts_receivable']->getKey(),
                'purchase_expense' => $accounts['purchase_expense']->getKey(),
                'purchase_tax' => $accounts['purchase_tax']->getKey(),
                'accounts_payable' => $accounts['accounts_payable']->getKey(),
            ],
            'posting_requires_approval' => false,
        ]);

        $invoice = SalesInvoice::query()->create([
            'company_id' => $company->getKey(),
            'fiscal_year_id' => $fiscalYear->getKey(),
            'party_id' => $party->getKey(),
            'invoice_no' => 'SI-3001',
            'invoice_date' => now()->toDateString(),
            'status' => 'draft',
            'discount_total' => 0,
            'is_official' => false,
        ]);

        $invoice->lines()->create([
            'description' => 'Item C',
            'quantity' => 1,
            'unit_price' => 1500,
            'tax_rate' => 9,
            'tax_amount' => 135,
            'line_total' => 1500,
        ]);

        $issued = app(SalesInvoiceService::class)->issue($invoice);

        $entry = JournalEntry::query()
            ->where('source_type', 'sales_invoice')
            ->where('source_id', $issued->getKey())
            ->with('lines')
            ->first();

        $this->assertNotNull($entry);
        $this->assertSame('posted', $entry->status);

        $lines = $entry->lines->keyBy('account_id');
        $this->assertSame(1635.0, (float) $lines[$accounts['accounts_receivable']->getKey()]->debit);
        $this->assertSame(1500.0, (float) $lines[$accounts['sales_revenue']->getKey()]->credit);
        $this->assertSame(135.0, (float) $lines[$accounts['sales_tax']->getKey()]->credit);
    }

    private function seedAccounts(): array
    {
        $company = AccountingCompany::query()->create([
            'name' => 'Alpha',
        ]);

        $fiscalYear = FiscalYear::query()->create([
            'company_id' => $company->getKey(),
            'name' => '1404',
            'start_date' => now()->startOfYear()->toDateString(),
            'end_date' => now()->endOfYear()->toDateString(),
        ]);

        $type = AccountType::query()->create([
            'code' => 'asset',
            'name' => 'دارایی',
            'normal_balance' => 'debit',
            'is_system' => true,
        ]);

        $plan = AccountPlan::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'پیش‌فرض',
            'is_default' => true,
        ]);

        $accounts = [
            'accounts_receivable' => ChartAccount::query()->create([
                'company_id' => $company->getKey(),
                'plan_id' => $plan->getKey(),
                'type_id' => $type->getKey(),
                'code' => '1101',
                'name' => 'حساب‌های دریافتنی',
                'level' => 3,
                'is_postable' => true,
            ]),
            'sales_revenue' => ChartAccount::query()->create([
                'company_id' => $company->getKey(),
                'plan_id' => $plan->getKey(),
                'type_id' => $type->getKey(),
                'code' => '4101',
                'name' => 'درآمد فروش',
                'level' => 3,
                'is_postable' => true,
            ]),
            'sales_tax' => ChartAccount::query()->create([
                'company_id' => $company->getKey(),
                'plan_id' => $plan->getKey(),
                'type_id' => $type->getKey(),
                'code' => '2101',
                'name' => 'مالیات فروش',
                'level' => 3,
                'is_postable' => true,
            ]),
            'purchase_expense' => ChartAccount::query()->create([
                'company_id' => $company->getKey(),
                'plan_id' => $plan->getKey(),
                'type_id' => $type->getKey(),
                'code' => '5101',
                'name' => 'هزینه خرید',
                'level' => 3,
                'is_postable' => true,
            ]),
            'purchase_tax' => ChartAccount::query()->create([
                'company_id' => $company->getKey(),
                'plan_id' => $plan->getKey(),
                'type_id' => $type->getKey(),
                'code' => '2102',
                'name' => 'مالیات خرید',
                'level' => 3,
                'is_postable' => true,
            ]),
            'accounts_payable' => ChartAccount::query()->create([
                'company_id' => $company->getKey(),
                'plan_id' => $plan->getKey(),
                'type_id' => $type->getKey(),
                'code' => '2103',
                'name' => 'حساب‌های پرداختنی',
                'level' => 3,
                'is_postable' => true,
            ]),
        ];

        return [$company, $fiscalYear, $accounts];
    }
}
