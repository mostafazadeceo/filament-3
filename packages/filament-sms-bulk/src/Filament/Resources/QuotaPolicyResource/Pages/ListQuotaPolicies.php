<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\QuotaPolicyResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Haida\SmsBulk\Filament\Resources\QuotaPolicyResource;

class ListQuotaPolicies extends ListRecords
{
    protected static string $resource = QuotaPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
