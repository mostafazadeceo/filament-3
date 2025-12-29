<?php

namespace Vendor\FilamentAccountingIr\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
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

final class AccountingCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-accounting-ir',
            self::permissions(),
            [
                'accounting' => true,
            ],
            [],
            [
                AccountingCompanyPolicy::class,
                AccountingCompanySettingPolicy::class,
                AccountingBranchPolicy::class,
                FiscalYearPolicy::class,
                FiscalPeriodPolicy::class,
                AccountPlanPolicy::class,
                ChartAccountPolicy::class,
                DimensionPolicy::class,
                JournalEntryPolicy::class,
                PartyPolicy::class,
                TaxCategoryPolicy::class,
                UomPolicy::class,
                ProductServicePolicy::class,
                TaxRatePolicy::class,
                VatPeriodPolicy::class,
                VatReportPolicy::class,
                WithholdingRatePolicy::class,
                SeasonalReportPolicy::class,
                SalesInvoicePolicy::class,
                PurchaseInvoicePolicy::class,
                TreasuryAccountPolicy::class,
                TreasuryTransactionPolicy::class,
                ChequePolicy::class,
                InventoryWarehousePolicy::class,
                InventoryItemPolicy::class,
                InventoryDocPolicy::class,
                FixedAssetPolicy::class,
                EmployeePolicy::class,
                PayrollRunPolicy::class,
                PayrollTablePolicy::class,
                ProjectPolicy::class,
                ContractPolicy::class,
                EInvoiceProviderPolicy::class,
                KeyMaterialPolicy::class,
                EInvoicePolicy::class,
                IntegrationConnectorPolicy::class,
            ],
            [
                'accounting' => 'حسابداری ایران',
                'accounting_core' => 'هسته حسابداری',
                'accounting_masterdata' => 'اطلاعات پایه',
                'accounting_sales' => 'فروش و دریافتنی‌ها',
                'accounting_purchase' => 'خرید و پرداختنی‌ها',
                'accounting_treasury' => 'خزانه و بانک',
                'accounting_inventory' => 'انبار',
                'accounting_fixed_assets' => 'دارایی ثابت',
                'accounting_payroll' => 'حقوق و دستمزد',
                'accounting_projects' => 'پروژه و پیمانکاری',
                'accounting_tax' => 'مالیات و انطباق',
                'accounting_einvoice' => 'سامانه مؤدیان',
                'accounting_integration' => 'یکپارچه‌سازی',
            ]
        );

        self::$registered = true;
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'accounting.view',
            'accounting.company.view',
            'accounting.company.manage',
            'accounting.company_settings.view',
            'accounting.company_settings.manage',
            'accounting.branch.view',
            'accounting.branch.manage',
            'accounting.fiscal_year.view',
            'accounting.fiscal_year.manage',
            'accounting.account_plan.view',
            'accounting.account_plan.manage',
            'accounting.chart_account.view',
            'accounting.chart_account.manage',
            'accounting.dimension.view',
            'accounting.dimension.manage',
            'accounting.party.view',
            'accounting.party.manage',
            'accounting.product.view',
            'accounting.product.manage',
            'accounting.tax_category.view',
            'accounting.tax_category.manage',
            'accounting.uom.view',
            'accounting.uom.manage',
            'accounting.tax_rate.view',
            'accounting.tax_rate.manage',
            'accounting.vat_period.view',
            'accounting.vat_period.manage',
            'accounting.vat_report.view',
            'accounting.vat_report.manage',
            'accounting.withholding_rate.view',
            'accounting.withholding_rate.manage',
            'accounting.seasonal_report.view',
            'accounting.seasonal_report.manage',
            'accounting.journal.view',
            'accounting.journal.create',
            'accounting.journal.submit',
            'accounting.journal.approve',
            'accounting.journal.post',
            'accounting.journal.reverse',
            'accounting.journal.void',
            'accounting.journal.export',
            'accounting.period.lock',
            'accounting.period.unlock',
            'accounting.sales.view',
            'accounting.sales.manage',
            'accounting.purchase.view',
            'accounting.purchase.manage',
            'accounting.treasury.view',
            'accounting.treasury.manage',
            'accounting.inventory.view',
            'accounting.inventory.manage',
            'accounting.inventory.post',
            'accounting.fixed_assets.view',
            'accounting.fixed_assets.manage',
            'accounting.payroll.view',
            'accounting.payroll.manage',
            'accounting.project.view',
            'accounting.project.manage',
            'accounting.contract.view',
            'accounting.contract.manage',
            'accounting.tax.view',
            'accounting.tax.manage',
            'accounting.tax.submit',
            'accounting.einvoice.view',
            'accounting.einvoice.manage',
            'accounting.einvoice.send',
            'accounting.einvoice.retry',
            'accounting.einvoice.keys.manage',
            'accounting.report.view',
            'accounting.report.export',
            'accounting.integration.view',
            'accounting.integration.manage',
        ];
    }
}
