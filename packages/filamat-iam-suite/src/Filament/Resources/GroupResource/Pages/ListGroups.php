<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\GroupResource\Pages;

use Filamat\IamSuite\Filament\Resources\GroupResource;
use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;

class ListGroups extends ListRecordsWithCreate
{
    protected static string $resource = GroupResource::class;
}
