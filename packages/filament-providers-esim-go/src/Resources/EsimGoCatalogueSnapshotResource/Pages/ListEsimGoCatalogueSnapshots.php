<?php

declare(strict_types=1);

namespace Haida\FilamentProvidersEsimGo\Resources\EsimGoCatalogueSnapshotResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoCatalogueSnapshotResource;

class ListEsimGoCatalogueSnapshots extends ListRecordsWithCreate
{
    protected static string $resource = EsimGoCatalogueSnapshotResource::class;

    protected function canCreate(): bool
    {
        return false;
    }
}
