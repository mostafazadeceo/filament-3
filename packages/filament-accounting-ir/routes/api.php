<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Illuminate\Support\Facades\Route;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\AccountingBranchController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\AccountingCompanyController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\AccountingCompanySettingController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\AccountingReportController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\AccountPlanController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\ChartAccountController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\ChequeController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\ContractController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\DimensionController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\EInvoiceController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\EInvoiceProviderController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\EmployeeController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\FiscalPeriodController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\FiscalYearController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\FixedAssetController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\IntegrationConnectorController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\InventoryDocController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\InventoryItemController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\InventoryWarehouseController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\JournalEntryController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\KeyMaterialController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\OpenApiController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\PartyController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\PayrollRunController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\PayrollTableController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\ProductServiceController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\ProjectController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\PurchaseInvoiceController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\SalesInvoiceController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\SeasonalReportController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\TaxCategoryController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\TaxRateController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\TreasuryAccountController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\TreasuryTransactionController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\UomController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\VatPeriodController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\VatReportController;
use Vendor\FilamentAccountingIr\Http\Controllers\Api\V1\WithholdingRateController;

Route::prefix('api/v1/accounting-ir')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-accounting-ir.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::apiResource('companies', AccountingCompanyController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.company.view');
        Route::apiResource('companies', AccountingCompanyController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.company.manage');

        Route::apiResource('company-settings', AccountingCompanySettingController::class)
            ->only(['index', 'show'])
            ->parameters(['company-settings' => 'company_setting'])
            ->middleware('filamat-iam.scope:accounting.company_settings.view');
        Route::apiResource('company-settings', AccountingCompanySettingController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['company-settings' => 'company_setting'])
            ->middleware('filamat-iam.scope:accounting.company_settings.manage');

        Route::apiResource('branches', AccountingBranchController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.branch.view');
        Route::apiResource('branches', AccountingBranchController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.branch.manage');

        Route::apiResource('fiscal-years', FiscalYearController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.fiscal_year.view');
        Route::apiResource('fiscal-years', FiscalYearController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.fiscal_year.manage');

        Route::apiResource('fiscal-periods', FiscalPeriodController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.fiscal_year.view');
        Route::apiResource('fiscal-periods', FiscalPeriodController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.fiscal_year.manage');

        Route::apiResource('account-plans', AccountPlanController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.account_plan.view');
        Route::apiResource('account-plans', AccountPlanController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.account_plan.manage');

        Route::apiResource('chart-accounts', ChartAccountController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.chart_account.view');
        Route::apiResource('chart-accounts', ChartAccountController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.chart_account.manage');

        Route::apiResource('dimensions', DimensionController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.dimension.view');
        Route::apiResource('dimensions', DimensionController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.dimension.manage');

        Route::apiResource('journal-entries', JournalEntryController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.journal.view');
        Route::apiResource('journal-entries', JournalEntryController::class)
            ->only(['store', 'update'])
            ->middleware('filamat-iam.scope:accounting.journal.create');

        Route::apiResource('parties', PartyController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.party.view');
        Route::apiResource('parties', PartyController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.party.manage');

        Route::apiResource('products', ProductServiceController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.product.view');
        Route::apiResource('products', ProductServiceController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.product.manage');

        Route::apiResource('tax-categories', TaxCategoryController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.tax_category.view');
        Route::apiResource('tax-categories', TaxCategoryController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.tax_category.manage');

        Route::apiResource('uoms', UomController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.uom.view');
        Route::apiResource('uoms', UomController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.uom.manage');

        Route::apiResource('tax-rates', TaxRateController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.tax_rate.view');
        Route::apiResource('tax-rates', TaxRateController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.tax_rate.manage');

        Route::apiResource('vat-periods', VatPeriodController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.vat_period.view');
        Route::apiResource('vat-periods', VatPeriodController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.vat_period.manage');

        Route::post('vat-periods/{vat_period}/generate', [VatPeriodController::class, 'generate'])
            ->middleware('filamat-iam.scope:accounting.vat_report.manage');

        Route::apiResource('vat-reports', VatReportController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.vat_report.view');
        Route::apiResource('vat-reports', VatReportController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.vat_report.manage');
        Route::post('vat-reports/{vat_report}/submit', [VatReportController::class, 'submit'])
            ->middleware('filamat-iam.scope:accounting.vat_report.manage');

        Route::apiResource('withholding-rates', WithholdingRateController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.withholding_rate.view');
        Route::apiResource('withholding-rates', WithholdingRateController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.withholding_rate.manage');

        Route::apiResource('seasonal-reports', SeasonalReportController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.seasonal_report.view');
        Route::apiResource('seasonal-reports', SeasonalReportController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.seasonal_report.manage');

        Route::apiResource('sales-invoices', SalesInvoiceController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.sales.view');
        Route::apiResource('sales-invoices', SalesInvoiceController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.sales.manage');
        Route::post('sales-invoices/{sales_invoice}/issue', [SalesInvoiceController::class, 'issue'])
            ->middleware('filamat-iam.scope:accounting.sales.manage');

        Route::apiResource('purchase-invoices', PurchaseInvoiceController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.purchase.view');
        Route::apiResource('purchase-invoices', PurchaseInvoiceController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.purchase.manage');
        Route::post('purchase-invoices/{purchase_invoice}/receive', [PurchaseInvoiceController::class, 'receive'])
            ->middleware('filamat-iam.scope:accounting.purchase.manage');

        Route::apiResource('treasury-accounts', TreasuryAccountController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.treasury.view');
        Route::apiResource('treasury-accounts', TreasuryAccountController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.treasury.manage');

        Route::apiResource('treasury-transactions', TreasuryTransactionController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.treasury.view');
        Route::apiResource('treasury-transactions', TreasuryTransactionController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.treasury.manage');

        Route::apiResource('cheques', ChequeController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.treasury.view');
        Route::apiResource('cheques', ChequeController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.treasury.manage');

        Route::apiResource('warehouses', InventoryWarehouseController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.inventory.view');
        Route::apiResource('warehouses', InventoryWarehouseController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.inventory.manage');

        Route::apiResource('inventory-items', InventoryItemController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.inventory.view');
        Route::apiResource('inventory-items', InventoryItemController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.inventory.manage');

        Route::apiResource('inventory-docs', InventoryDocController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.inventory.view');
        Route::apiResource('inventory-docs', InventoryDocController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.inventory.manage');
        Route::post('inventory-docs/{inventory_doc}/post', [InventoryDocController::class, 'post'])
            ->middleware('filamat-iam.scope:accounting.inventory.post');

        Route::apiResource('fixed-assets', FixedAssetController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.fixed_assets.view');
        Route::apiResource('fixed-assets', FixedAssetController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.fixed_assets.manage');

        Route::apiResource('employees', EmployeeController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.payroll.view');
        Route::apiResource('employees', EmployeeController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.payroll.manage');

        Route::apiResource('payroll-runs', PayrollRunController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.payroll.view');
        Route::apiResource('payroll-runs', PayrollRunController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.payroll.manage');

        Route::apiResource('payroll-tables', PayrollTableController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.payroll.view');
        Route::apiResource('payroll-tables', PayrollTableController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.payroll.manage');

        Route::apiResource('projects', ProjectController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.project.view');
        Route::apiResource('projects', ProjectController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.project.manage');

        Route::apiResource('contracts', ContractController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.contract.view');
        Route::apiResource('contracts', ContractController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.contract.manage');

        Route::apiResource('e-invoice-providers', EInvoiceProviderController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.einvoice.view');
        Route::apiResource('e-invoice-providers', EInvoiceProviderController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.einvoice.manage');

        Route::apiResource('e-invoices', EInvoiceController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.einvoice.view');
        Route::apiResource('e-invoices', EInvoiceController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.einvoice.manage');
        Route::post('e-invoices/{e_invoice}/send', [EInvoiceController::class, 'send'])
            ->middleware('filamat-iam.scope:accounting.einvoice.send');

        Route::apiResource('key-materials', KeyMaterialController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:accounting.einvoice.view');
        Route::apiResource('key-materials', KeyMaterialController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:accounting.einvoice.keys.manage');

        Route::apiResource('integrations', IntegrationConnectorController::class)
            ->only(['index', 'show'])
            ->parameters(['integrations' => 'integration_connector'])
            ->middleware('filamat-iam.scope:accounting.integration.view');
        Route::apiResource('integrations', IntegrationConnectorController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['integrations' => 'integration_connector'])
            ->middleware('filamat-iam.scope:accounting.integration.manage');
        Route::post('integrations/{integration_connector}/run', [IntegrationConnectorController::class, 'run'])
            ->middleware('filamat-iam.scope:accounting.integration.manage');

        Route::get('reports/trial-balance', [AccountingReportController::class, 'trialBalance'])
            ->middleware('filamat-iam.scope:accounting.report.view');
        Route::get('reports/general-ledger', [AccountingReportController::class, 'generalLedger'])
            ->middleware('filamat-iam.scope:accounting.report.view');

        Route::post('journal-entries/{journalEntry}/submit', [JournalEntryController::class, 'submit'])
            ->middleware('filamat-iam.scope:accounting.journal.submit');
        Route::post('journal-entries/{journalEntry}/approve', [JournalEntryController::class, 'approve'])
            ->middleware('filamat-iam.scope:accounting.journal.approve');
        Route::post('journal-entries/{journalEntry}/post', [JournalEntryController::class, 'post'])
            ->middleware('filamat-iam.scope:accounting.journal.post');
        Route::post('journal-entries/{journalEntry}/reverse', [JournalEntryController::class, 'reverse'])
            ->middleware('filamat-iam.scope:accounting.journal.reverse');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:accounting.view');
    });
