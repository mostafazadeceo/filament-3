<?php

namespace Vendor\FilamentAccountingIr;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vendor\FilamentAccountingIr\Console\Commands\InstallAccountingIr;
use Vendor\FilamentAccountingIr\Console\Commands\RunAccountingIntegrations;
use Vendor\FilamentAccountingIr\Listeners\AccountingEventSubscriber;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\AccountingCompanySetting;
use Vendor\FilamentAccountingIr\Models\AccountPlan;
use Vendor\FilamentAccountingIr\Models\ChartAccount;
use Vendor\FilamentAccountingIr\Models\Cheque;
use Vendor\FilamentAccountingIr\Models\Contract;
use Vendor\FilamentAccountingIr\Models\Dimension;
use Vendor\FilamentAccountingIr\Models\EInvoice;
use Vendor\FilamentAccountingIr\Models\EInvoiceProvider;
use Vendor\FilamentAccountingIr\Models\Employee;
use Vendor\FilamentAccountingIr\Models\FiscalPeriod;
use Vendor\FilamentAccountingIr\Models\FiscalYear;
use Vendor\FilamentAccountingIr\Models\FixedAsset;
use Vendor\FilamentAccountingIr\Models\IntegrationConnector;
use Vendor\FilamentAccountingIr\Models\InventoryDoc;
use Vendor\FilamentAccountingIr\Models\InventoryItem;
use Vendor\FilamentAccountingIr\Models\InventoryWarehouse;
use Vendor\FilamentAccountingIr\Models\JournalEntry;
use Vendor\FilamentAccountingIr\Models\JournalLine;
use Vendor\FilamentAccountingIr\Models\KeyMaterial;
use Vendor\FilamentAccountingIr\Models\Party;
use Vendor\FilamentAccountingIr\Models\PayrollRun;
use Vendor\FilamentAccountingIr\Models\PayrollTable;
use Vendor\FilamentAccountingIr\Models\ProductService;
use Vendor\FilamentAccountingIr\Models\Project;
use Vendor\FilamentAccountingIr\Models\PurchaseInvoice;
use Vendor\FilamentAccountingIr\Models\SalesInvoice;
use Vendor\FilamentAccountingIr\Models\SeasonalReport;
use Vendor\FilamentAccountingIr\Models\TaxCategory;
use Vendor\FilamentAccountingIr\Models\TaxRate;
use Vendor\FilamentAccountingIr\Models\TreasuryAccount;
use Vendor\FilamentAccountingIr\Models\TreasuryTransaction;
use Vendor\FilamentAccountingIr\Models\Uom;
use Vendor\FilamentAccountingIr\Models\VatPeriod;
use Vendor\FilamentAccountingIr\Models\VatReport;
use Vendor\FilamentAccountingIr\Models\WithholdingRate;
use Vendor\FilamentAccountingIr\Observers\JournalLineObserver;
use Vendor\FilamentAccountingIr\Policies\AccountingBranchPolicy;
use Vendor\FilamentAccountingIr\Policies\AccountingCompanyPolicy;
use Vendor\FilamentAccountingIr\Policies\AccountingCompanySettingPolicy;
use Vendor\FilamentAccountingIr\Policies\AccountPlanPolicy;
use Vendor\FilamentAccountingIr\Policies\ChartAccountPolicy;
use Vendor\FilamentAccountingIr\Policies\ChequePolicy;
use Vendor\FilamentAccountingIr\Policies\ContractPolicy;
use Vendor\FilamentAccountingIr\Policies\DimensionPolicy;
use Vendor\FilamentAccountingIr\Policies\EInvoicePolicy;
use Vendor\FilamentAccountingIr\Policies\EInvoiceProviderPolicy;
use Vendor\FilamentAccountingIr\Policies\EmployeePolicy;
use Vendor\FilamentAccountingIr\Policies\FiscalPeriodPolicy;
use Vendor\FilamentAccountingIr\Policies\FiscalYearPolicy;
use Vendor\FilamentAccountingIr\Policies\FixedAssetPolicy;
use Vendor\FilamentAccountingIr\Policies\IntegrationConnectorPolicy;
use Vendor\FilamentAccountingIr\Policies\InventoryDocPolicy;
use Vendor\FilamentAccountingIr\Policies\InventoryItemPolicy;
use Vendor\FilamentAccountingIr\Policies\InventoryWarehousePolicy;
use Vendor\FilamentAccountingIr\Policies\JournalEntryPolicy;
use Vendor\FilamentAccountingIr\Policies\KeyMaterialPolicy;
use Vendor\FilamentAccountingIr\Policies\PartyPolicy;
use Vendor\FilamentAccountingIr\Policies\PayrollRunPolicy;
use Vendor\FilamentAccountingIr\Policies\PayrollTablePolicy;
use Vendor\FilamentAccountingIr\Policies\ProductServicePolicy;
use Vendor\FilamentAccountingIr\Policies\ProjectPolicy;
use Vendor\FilamentAccountingIr\Policies\PurchaseInvoicePolicy;
use Vendor\FilamentAccountingIr\Policies\SalesInvoicePolicy;
use Vendor\FilamentAccountingIr\Policies\SeasonalReportPolicy;
use Vendor\FilamentAccountingIr\Policies\TaxCategoryPolicy;
use Vendor\FilamentAccountingIr\Policies\TaxRatePolicy;
use Vendor\FilamentAccountingIr\Policies\TreasuryAccountPolicy;
use Vendor\FilamentAccountingIr\Policies\TreasuryTransactionPolicy;
use Vendor\FilamentAccountingIr\Policies\UomPolicy;
use Vendor\FilamentAccountingIr\Policies\VatPeriodPolicy;
use Vendor\FilamentAccountingIr\Policies\VatReportPolicy;
use Vendor\FilamentAccountingIr\Policies\WithholdingRatePolicy;
use Vendor\FilamentAccountingIr\Support\AccountingCapabilities;

class FilamentAccountingIrServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-accounting-ir')
            ->hasConfigFile('filament-accounting-ir')
            ->hasTranslations()
            ->hasRoutes('api')
            ->hasCommands([
                InstallAccountingIr::class,
                RunAccountingIntegrations::class,
            ])
            ->hasMigrations([
                '2025_12_28_000001_create_accounting_ir_core_tables',
                '2025_12_28_000002_create_accounting_ir_chart_tables',
                '2025_12_28_000003_create_accounting_ir_journal_tables',
                '2025_12_28_000004_create_accounting_ir_party_tables',
                '2025_12_28_000005_create_accounting_ir_treasury_tables',
                '2025_12_28_000006_create_accounting_ir_sales_tables',
                '2025_12_28_000007_create_accounting_ir_purchase_tables',
                '2025_12_28_000008_create_accounting_ir_inventory_tables',
                '2025_12_28_000009_create_accounting_ir_fixed_asset_tables',
                '2025_12_28_000010_create_accounting_ir_payroll_tables',
                '2025_12_28_000011_create_accounting_ir_project_tables',
                '2025_12_28_000012_create_accounting_ir_tax_tables',
                '2025_12_28_000013_create_accounting_ir_e_invoice_tables',
                '2025_12_28_000014_create_accounting_ir_integration_tables',
                '2025_12_28_000015_create_accounting_ir_audit_tables',
                '2025_12_28_000016_create_accounting_ir_company_settings_table',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        Gate::policy(AccountingCompany::class, AccountingCompanyPolicy::class);
        Gate::policy(AccountingCompanySetting::class, AccountingCompanySettingPolicy::class);
        Gate::policy(AccountingBranch::class, AccountingBranchPolicy::class);
        Gate::policy(FiscalYear::class, FiscalYearPolicy::class);
        Gate::policy(FiscalPeriod::class, FiscalPeriodPolicy::class);
        Gate::policy(AccountPlan::class, AccountPlanPolicy::class);
        Gate::policy(ChartAccount::class, ChartAccountPolicy::class);
        Gate::policy(Dimension::class, DimensionPolicy::class);
        Gate::policy(JournalEntry::class, JournalEntryPolicy::class);
        Gate::policy(Party::class, PartyPolicy::class);
        Gate::policy(ProductService::class, ProductServicePolicy::class);
        Gate::policy(TaxCategory::class, TaxCategoryPolicy::class);
        Gate::policy(TaxRate::class, TaxRatePolicy::class);
        Gate::policy(VatPeriod::class, VatPeriodPolicy::class);
        Gate::policy(VatReport::class, VatReportPolicy::class);
        Gate::policy(WithholdingRate::class, WithholdingRatePolicy::class);
        Gate::policy(SeasonalReport::class, SeasonalReportPolicy::class);
        Gate::policy(EInvoiceProvider::class, EInvoiceProviderPolicy::class);
        Gate::policy(EInvoice::class, EInvoicePolicy::class);
        Gate::policy(KeyMaterial::class, KeyMaterialPolicy::class);
        Gate::policy(SalesInvoice::class, SalesInvoicePolicy::class);
        Gate::policy(PurchaseInvoice::class, PurchaseInvoicePolicy::class);
        Gate::policy(TreasuryAccount::class, TreasuryAccountPolicy::class);
        Gate::policy(TreasuryTransaction::class, TreasuryTransactionPolicy::class);
        Gate::policy(Cheque::class, ChequePolicy::class);
        Gate::policy(InventoryWarehouse::class, InventoryWarehousePolicy::class);
        Gate::policy(InventoryItem::class, InventoryItemPolicy::class);
        Gate::policy(InventoryDoc::class, InventoryDocPolicy::class);
        Gate::policy(FixedAsset::class, FixedAssetPolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(PayrollRun::class, PayrollRunPolicy::class);
        Gate::policy(PayrollTable::class, PayrollTablePolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Contract::class, ContractPolicy::class);
        Gate::policy(IntegrationConnector::class, IntegrationConnectorPolicy::class);
        Gate::policy(Uom::class, UomPolicy::class);

        JournalLine::observe(JournalLineObserver::class);
        Event::subscribe(AccountingEventSubscriber::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            AccountingCapabilities::register($registry);
        }

        Gate::define('accounting.view', fn () => IamAuthorization::allows('accounting.view'));
    }
}
