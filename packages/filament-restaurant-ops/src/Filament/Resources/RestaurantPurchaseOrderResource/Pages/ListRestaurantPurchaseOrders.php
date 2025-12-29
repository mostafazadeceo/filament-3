<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseOrderResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseOrderResource;

class ListRestaurantPurchaseOrders extends ListRecordsWithCreate
{
    protected static string $resource = RestaurantPurchaseOrderResource::class;
}
