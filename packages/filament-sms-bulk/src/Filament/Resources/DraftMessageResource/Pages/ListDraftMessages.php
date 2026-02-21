<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\DraftMessageResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Haida\SmsBulk\Filament\Resources\DraftMessageResource;

class ListDraftMessages extends ListRecords
{
    protected static string $resource = DraftMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
