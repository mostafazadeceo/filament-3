<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\QuietHoursProfileResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Haida\SmsBulk\Filament\Resources\QuietHoursProfileResource;

class ListQuietHoursProfiles extends ListRecords
{
    protected static string $resource = QuietHoursProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
