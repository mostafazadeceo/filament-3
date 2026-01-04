<?php

namespace Haida\CommerceCatalog\Filament\Resources\CatalogCollectionResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\CommerceCatalog\Filament\Resources\CatalogCollectionResource;

class ListCatalogCollections extends ListRecordsWithCreate
{
    protected static string $resource = CatalogCollectionResource::class;
}
