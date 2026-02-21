<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\SenderIdentityResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Haida\SmsBulk\Filament\Resources\SenderIdentityResource;

class ListSenderIdentities extends ListRecords
{
    protected static string $resource = SenderIdentityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
