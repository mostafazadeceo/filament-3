<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\ConsentRegistryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Haida\SmsBulk\Filament\Resources\ConsentRegistryResource;

class EditConsent extends EditRecord
{
    protected static string $resource = ConsentRegistryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
