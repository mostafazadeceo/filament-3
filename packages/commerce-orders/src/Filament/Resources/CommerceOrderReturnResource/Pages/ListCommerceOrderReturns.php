<?php

namespace Haida\CommerceOrders\Filament\Resources\CommerceOrderReturnResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderReturnResource;

class ListCommerceOrderReturns extends ListRecordsWithCreate
{
    protected static string $resource = CommerceOrderReturnResource::class;
}
