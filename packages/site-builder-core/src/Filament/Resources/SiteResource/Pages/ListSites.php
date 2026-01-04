<?php

namespace Haida\SiteBuilderCore\Filament\Resources\SiteResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\SiteBuilderCore\Filament\Resources\SiteResource;

class ListSites extends ListRecordsWithCreate
{
    protected static string $resource = SiteResource::class;
}
