<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRecordsWithCreate extends ListRecords
{
    /**
     * @return array<Action | ActionGroup>
     */
    protected function getTableEmptyStateActions(): array
    {
        $resource = static::getResource();

        if (! $resource::hasPage('create')) {
            return [];
        }

        $label = $resource::getModelLabel();
        if (preg_match('/[A-Za-z]/', $label)) {
            $label = $resource::getNavigationLabel();
        }

        return [
            CreateAction::make()
                ->label('ایجاد '.$label)
                ->visible(fn (): bool => $resource::canCreate()),
        ];
    }

    protected function getHeaderActions(): array
    {
        $resource = static::getResource();

        if (! $resource::hasPage('create')) {
            return [];
        }

        $label = $resource::getModelLabel();
        if (preg_match('/[A-Za-z]/', $label)) {
            $label = $resource::getNavigationLabel();
        }

        return [
            CreateAction::make()
                ->label('ایجاد '.$label)
                ->visible(fn (): bool => $resource::canCreate()),
        ];
    }
}
