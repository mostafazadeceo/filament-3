<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\PatternTemplateResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Haida\SmsBulk\Filament\Resources\PatternTemplateResource;

class EditPatternTemplate extends EditRecord
{
    protected static string $resource = PatternTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
