<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\ProviderConnectionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Haida\SmsBulk\Filament\Resources\ProviderConnectionResource;

class ListProviderConnections extends ListRecords
{
    protected static string $resource = ProviderConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
