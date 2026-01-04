<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\PrivilegeRequestResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\PrivilegeRequestResource;

class ListPrivilegeRequests extends ListRecordsWithCreate
{
    protected static string $resource = PrivilegeRequestResource::class;
}
