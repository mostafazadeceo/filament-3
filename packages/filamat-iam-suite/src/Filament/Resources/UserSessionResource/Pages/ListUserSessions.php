<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserSessionResource\Pages;

use Filamat\IamSuite\Filament\Resources\UserSessionResource;
use Filament\Resources\Pages\ListRecords;

class ListUserSessions extends ListRecords
{
    protected static string $resource = UserSessionResource::class;
}
