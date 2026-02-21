<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\QuotaPolicyResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Haida\SmsBulk\Filament\Resources\QuotaPolicyResource;

class EditQuotaPolicy extends EditRecord
{
    protected static string $resource = QuotaPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
