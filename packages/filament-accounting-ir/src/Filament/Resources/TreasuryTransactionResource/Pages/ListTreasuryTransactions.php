<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\TreasuryTransactionResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\TreasuryTransactionResource;

class ListTreasuryTransactions extends ListRecordsWithCreate
{
    protected static string $resource = TreasuryTransactionResource::class;
}
