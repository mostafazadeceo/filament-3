<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\QuietHoursProfileResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Haida\SmsBulk\Filament\Resources\QuietHoursProfileResource;

class EditQuietHoursProfile extends EditRecord
{
    protected static string $resource = QuietHoursProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
