<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\SenderIdentityResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Haida\SmsBulk\Filament\Resources\SenderIdentityResource;

class EditSenderIdentity extends EditRecord
{
    protected static string $resource = SenderIdentityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
