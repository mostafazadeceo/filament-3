<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\DraftMessageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Haida\SmsBulk\Filament\Resources\DraftMessageResource;

class EditDraftMessage extends EditRecord
{
    protected static string $resource = DraftMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
