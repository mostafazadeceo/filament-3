<?php

namespace Vendor\FilamentAccountingIr;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountingBranchResource;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountingCompanyResource;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountingCompanySettingResource;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountPlanResource;
use Vendor\FilamentAccountingIr\Filament\Resources\ChartAccountResource;
use Vendor\FilamentAccountingIr\Filament\Resources\ChequeResource;
use Vendor\FilamentAccountingIr\Filament\Resources\ContractResource;
use Vendor\FilamentAccountingIr\Filament\Resources\DimensionResource;
use Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceProviderResource;
use Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceResource;
use Vendor\FilamentAccountingIr\Filament\Resources\EmployeeResource;
use Vendor\FilamentAccountingIr\Filament\Resources\FiscalPeriodResource;
use Vendor\FilamentAccountingIr\Filament\Resources\FiscalYearResource;
use Vendor\FilamentAccountingIr\Filament\Resources\FixedAssetResource;
use Vendor\FilamentAccountingIr\Filament\Resources\IntegrationConnectorResource;
use Vendor\FilamentAccountingIr\Filament\Resources\InventoryDocResource;
use Vendor\FilamentAccountingIr\Filament\Resources\InventoryItemResource;
use Vendor\FilamentAccountingIr\Filament\Resources\InventoryWarehouseResource;
use Vendor\FilamentAccountingIr\Filament\Resources\JournalEntryResource;
use Vendor\FilamentAccountingIr\Filament\Resources\KeyMaterialResource;
use Vendor\FilamentAccountingIr\Filament\Resources\PartyResource;
use Vendor\FilamentAccountingIr\Filament\Resources\PayrollRunResource;
use Vendor\FilamentAccountingIr\Filament\Resources\PayrollTableResource;
use Vendor\FilamentAccountingIr\Filament\Resources\ProductServiceResource;
use Vendor\FilamentAccountingIr\Filament\Resources\ProjectResource;
use Vendor\FilamentAccountingIr\Filament\Resources\PurchaseInvoiceResource;
use Vendor\FilamentAccountingIr\Filament\Resources\SalesInvoiceResource;
use Vendor\FilamentAccountingIr\Filament\Resources\SeasonalReportResource;
use Vendor\FilamentAccountingIr\Filament\Resources\TaxCategoryResource;
use Vendor\FilamentAccountingIr\Filament\Resources\TaxRateResource;
use Vendor\FilamentAccountingIr\Filament\Resources\TreasuryAccountResource;
use Vendor\FilamentAccountingIr\Filament\Resources\TreasuryTransactionResource;
use Vendor\FilamentAccountingIr\Filament\Resources\UomResource;
use Vendor\FilamentAccountingIr\Filament\Resources\VatPeriodResource;
use Vendor\FilamentAccountingIr\Filament\Resources\WithholdingRateResource;

class FilamentAccountingIrPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'accounting-ir';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            AccountingCompanyResource::class,
            AccountingCompanySettingResource::class,
            AccountingBranchResource::class,
            FiscalYearResource::class,
            FiscalPeriodResource::class,
            AccountPlanResource::class,
            ChartAccountResource::class,
            DimensionResource::class,
            JournalEntryResource::class,
            PartyResource::class,
            TaxCategoryResource::class,
            UomResource::class,
            ProductServiceResource::class,
            SalesInvoiceResource::class,
            PurchaseInvoiceResource::class,
            TreasuryAccountResource::class,
            TreasuryTransactionResource::class,
            ChequeResource::class,
            InventoryWarehouseResource::class,
            InventoryItemResource::class,
            InventoryDocResource::class,
            FixedAssetResource::class,
            EmployeeResource::class,
            PayrollRunResource::class,
            PayrollTableResource::class,
            ProjectResource::class,
            ContractResource::class,
            TaxRateResource::class,
            VatPeriodResource::class,
            WithholdingRateResource::class,
            SeasonalReportResource::class,
            EInvoiceProviderResource::class,
            KeyMaterialResource::class,
            EInvoiceResource::class,
            IntegrationConnectorResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
