<?php

namespace Haida\CommerceCatalog\Filament\Resources\CatalogProductResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\CommerceCatalog\Filament\Resources\CatalogProductResource;

class ListCatalogProducts extends ListRecordsWithCreate
{
    protected static string $resource = CatalogProductResource::class;
}
