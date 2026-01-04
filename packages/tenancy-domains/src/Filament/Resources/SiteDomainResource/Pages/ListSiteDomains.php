<?php

namespace Haida\TenancyDomains\Filament\Resources\SiteDomainResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\TenancyDomains\Filament\Resources\SiteDomainResource;

class ListSiteDomains extends ListRecordsWithCreate
{
    protected static string $resource = SiteDomainResource::class;
}
