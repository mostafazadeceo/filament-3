<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\ProviderConnectionResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Haida\SmsBulk\Filament\Resources\ProviderConnectionResource;

class EditProviderConnection extends EditRecord
{
    protected static string $resource = ProviderConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
