<?php

declare(strict_types=1);

namespace Haida\FilamentProvidersEsimGo\Resources\EsimGoConnectionResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoConnectionResource;

class ListEsimGoConnections extends ListRecordsWithCreate
{
    protected static string $resource = EsimGoConnectionResource::class;
}
