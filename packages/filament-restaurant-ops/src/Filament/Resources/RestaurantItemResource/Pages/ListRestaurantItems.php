<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources\RestaurantItemResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantItemResource;

class ListRestaurantItems extends ListRecordsWithCreate
{
    protected static string $resource = RestaurantItemResource::class;
}
