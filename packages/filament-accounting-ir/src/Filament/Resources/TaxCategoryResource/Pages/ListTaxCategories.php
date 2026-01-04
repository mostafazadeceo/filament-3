<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\TaxCategoryResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\TaxCategoryResource;

class ListTaxCategories extends ListRecordsWithCreate
{
    protected static string $resource = TaxCategoryResource::class;
}
