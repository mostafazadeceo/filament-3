<?php

declare(strict_types=1);

namespace Haida\ProvidersCore\Filament\Resources\ProviderJobLogResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\ProvidersCore\Filament\Resources\ProviderJobLogResource;

class ListProviderJobLogs extends ListRecordsWithCreate
{
    protected static string $resource = ProviderJobLogResource::class;
}
