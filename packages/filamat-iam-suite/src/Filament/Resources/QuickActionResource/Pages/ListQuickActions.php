<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\QuickActionResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\QuickActionResource;

class ListQuickActions extends ListRecordsWithCreate
{
    protected static string $resource = QuickActionResource::class;

    public bool $isTableReordering = true;

    public function mount(): void
    {
        parent::mount();

        $this->isTableReordering = true;
    }
}
