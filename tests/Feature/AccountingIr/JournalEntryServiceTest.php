<?php

namespace Tests\Feature\AccountingIr;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\AccountPlan;
use Vendor\FilamentAccountingIr\Models\AccountType;
use Vendor\FilamentAccountingIr\Models\ChartAccount;
use Vendor\FilamentAccountingIr\Models\FiscalYear;
use Vendor\FilamentAccountingIr\Models\JournalEntry;
use Vendor\FilamentAccountingIr\Models\JournalLine;
use Vendor\FilamentAccountingIr\Services\JournalEntryService;

class JournalEntryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_posts_a_balanced_entry(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant A',
            'slug' => 'tenant-a',
        ]);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create([
            'name' => 'Acme',
            'legal_name' => 'Acme LLC',
            'national_id' => '1234567890',
            'economic_code' => 'EC-1',
        ]);

        $plan = AccountPlan::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'Default',
            'is_default' => true,
        ]);

        $type = AccountType::query()->create([
            'name' => 'Asset',
            'code' => 'asset',
        ]);

        $cash = ChartAccount::query()->create([
            'company_id' => $company->getKey(),
            'plan_id' => $plan->getKey(),
            'type_id' => $type->getKey(),
            'code' => '101',
            'name' => 'Cash',
            'level' => 3,
            'is_postable' => true,
        ]);

        $revenue = ChartAccount::query()->create([
            'company_id' => $company->getKey(),
            'plan_id' => $plan->getKey(),
            'type_id' => $type->getKey(),
            'code' => '400',
            'name' => 'Revenue',
            'level' => 3,
            'is_postable' => true,
        ]);

        $fiscalYear = FiscalYear::query()->create([
            'company_id' => $company->getKey(),
            'name' => '1403',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_closed' => false,
        ]);

        $entry = JournalEntry::query()->create([
            'company_id' => $company->getKey(),
            'fiscal_year_id' => $fiscalYear->getKey(),
            'entry_no' => 'JV-1',
            'entry_date' => now(),
            'status' => 'draft',
        ]);

        JournalLine::query()->create([
            'journal_entry_id' => $entry->getKey(),
            'company_id' => $company->getKey(),
            'account_id' => $cash->getKey(),
            'debit' => 1000,
            'credit' => 0,
        ]);

        JournalLine::query()->create([
            'journal_entry_id' => $entry->getKey(),
            'company_id' => $company->getKey(),
            'account_id' => $revenue->getKey(),
            'debit' => 0,
            'credit' => 1000,
        ]);

        $service = app(JournalEntryService::class);
        $service->post($entry->refresh());

        $entry->refresh();

        $this->assertSame('posted', $entry->status);
        $this->assertNotNull($entry->posted_at);
    }

    public function test_it_rejects_unbalanced_entry(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant B',
            'slug' => 'tenant-b',
        ]);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create([
            'name' => 'Beta',
        ]);

        $plan = AccountPlan::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'Default',
            'is_default' => true,
        ]);

        $type = AccountType::query()->create([
            'name' => 'Asset',
            'code' => 'asset',
        ]);

        $account = ChartAccount::query()->create([
            'company_id' => $company->getKey(),
            'plan_id' => $plan->getKey(),
            'type_id' => $type->getKey(),
            'code' => '101',
            'name' => 'Cash',
            'level' => 3,
            'is_postable' => true,
        ]);

        $fiscalYear = FiscalYear::query()->create([
            'company_id' => $company->getKey(),
            'name' => '1403',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_closed' => false,
        ]);

        $entry = JournalEntry::query()->create([
            'company_id' => $company->getKey(),
            'fiscal_year_id' => $fiscalYear->getKey(),
            'entry_no' => 'JV-2',
            'entry_date' => now(),
            'status' => 'draft',
        ]);

        JournalLine::query()->create([
            'journal_entry_id' => $entry->getKey(),
            'company_id' => $company->getKey(),
            'account_id' => $account->getKey(),
            'debit' => 1000,
            'credit' => 0,
        ]);

        $this->expectException(ValidationException::class);

        app(JournalEntryService::class)->post($entry->refresh());
    }
}
