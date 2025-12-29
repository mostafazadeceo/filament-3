<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\AccountingCompanyResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountingCompanyResource;

class ListAccountingCompanies extends ListRecordsWithCreate
{
    protected static string $resource = AccountingCompanyResource::class;
}
