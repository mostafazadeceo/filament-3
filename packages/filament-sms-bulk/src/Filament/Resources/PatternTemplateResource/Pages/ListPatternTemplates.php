<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\PatternTemplateResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Haida\SmsBulk\Filament\Resources\PatternTemplateResource;

class ListPatternTemplates extends ListRecords
{
    protected static string $resource = PatternTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
