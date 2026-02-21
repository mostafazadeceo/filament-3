<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\PhonebookResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Haida\SmsBulk\Filament\Resources\PhonebookResource;

class ListPhonebooks extends ListRecords
{
    protected static string $resource = PhonebookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
