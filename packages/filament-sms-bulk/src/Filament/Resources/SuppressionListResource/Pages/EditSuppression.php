<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\SuppressionListResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Haida\SmsBulk\Filament\Resources\SuppressionListResource;

class EditSuppression extends EditRecord
{
    protected static string $resource = SuppressionListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
