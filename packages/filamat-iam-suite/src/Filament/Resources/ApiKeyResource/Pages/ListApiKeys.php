<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\ApiKeyResource\Pages;

use Filamat\IamSuite\Filament\Resources\ApiKeyResource;
use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;

class ListApiKeys extends ListRecordsWithCreate
{
    protected static string $resource = ApiKeyResource::class;
}
