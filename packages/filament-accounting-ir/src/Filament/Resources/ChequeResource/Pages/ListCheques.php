<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\ChequeResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\ChequeResource;

class ListCheques extends ListRecordsWithCreate
{
    protected static string $resource = ChequeResource::class;
}
