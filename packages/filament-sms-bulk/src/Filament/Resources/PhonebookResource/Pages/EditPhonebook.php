<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\PhonebookResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Haida\SmsBulk\Filament\Resources\PhonebookResource;

class EditPhonebook extends EditRecord
{
    protected static string $resource = PhonebookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
