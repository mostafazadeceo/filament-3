<?php

namespace Haida\FilamentCommerceCore\Filament\Resources\CommerceCategoryResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceCategoryResource;

class ListCommerceCategories extends ListRecordsWithCreate
{
    protected static string $resource = CommerceCategoryResource::class;
}
