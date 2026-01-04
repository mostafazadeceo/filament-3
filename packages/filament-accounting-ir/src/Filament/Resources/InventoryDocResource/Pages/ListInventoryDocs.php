<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\InventoryDocResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\InventoryDocResource;

class ListInventoryDocs extends ListRecordsWithCreate
{
    protected static string $resource = InventoryDocResource::class;
}
