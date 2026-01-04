<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\InventoryItemResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\InventoryItemResource;

class ListInventoryItems extends ListRecordsWithCreate
{
    protected static string $resource = InventoryItemResource::class;
}
