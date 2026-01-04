<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\ImpersonationSessionResource\Pages;

use Filamat\IamSuite\Filament\Resources\ImpersonationSessionResource;
use Filament\Resources\Pages\ListRecords;

class ListImpersonationSessions extends ListRecords
{
    protected static string $resource = ImpersonationSessionResource::class;
}
