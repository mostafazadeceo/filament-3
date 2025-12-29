<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources\RestaurantMenuItemResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantMenuItemResource;

class ListRestaurantMenuItems extends ListRecordsWithCreate
{
    protected static string $resource = RestaurantMenuItemResource::class;
}
