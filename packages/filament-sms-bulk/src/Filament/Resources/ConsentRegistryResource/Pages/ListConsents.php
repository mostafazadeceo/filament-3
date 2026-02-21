<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\ConsentRegistryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Haida\SmsBulk\Filament\Resources\ConsentRegistryResource;

class ListConsents extends ListRecords
{
    protected static string $resource = ConsentRegistryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
