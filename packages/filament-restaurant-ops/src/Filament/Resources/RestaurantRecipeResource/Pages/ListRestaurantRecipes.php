<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources\RestaurantRecipeResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantRecipeResource;

class ListRestaurantRecipes extends ListRecordsWithCreate
{
    protected static string $resource = RestaurantRecipeResource::class;
}
