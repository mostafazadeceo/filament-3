<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\SuppressionListResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Haida\SmsBulk\Filament\Resources\SuppressionListResource;

class ListSuppressions extends ListRecords
{
    protected static string $resource = SuppressionListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
