<?php

declare(strict_types=1);

namespace Haida\FilamentProvidersEsimGo\Resources\EsimGoOrderResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoOrderResource;

class ListEsimGoOrders extends ListRecordsWithCreate
{
    protected static string $resource = EsimGoOrderResource::class;

    protected function canCreate(): bool
    {
        return false;
    }
}
