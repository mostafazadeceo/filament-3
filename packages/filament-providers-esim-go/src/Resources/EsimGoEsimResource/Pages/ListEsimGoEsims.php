<?php

declare(strict_types=1);

namespace Haida\FilamentProvidersEsimGo\Resources\EsimGoEsimResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoEsimResource;

class ListEsimGoEsims extends ListRecordsWithCreate
{
    protected static string $resource = EsimGoEsimResource::class;

    protected function canCreate(): bool
    {
        return false;
    }
}
