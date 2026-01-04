<?php

namespace Haida\PlatformCore\Filament\Resources\TenantPluginResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\PlatformCore\Filament\Resources\TenantPluginResource;

class ListTenantPlugins extends ListRecordsWithCreate
{
    protected static string $resource = TenantPluginResource::class;
}
