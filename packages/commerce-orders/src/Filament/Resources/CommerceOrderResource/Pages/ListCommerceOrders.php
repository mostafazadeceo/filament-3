<?php

namespace Haida\CommerceOrders\Filament\Resources\CommerceOrderResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderResource;

class ListCommerceOrders extends ListRecordsWithCreate
{
    protected static string $resource = CommerceOrderResource::class;
}
