<?php

namespace Haida\PageBuilder\Filament\Resources\PageTemplateResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\PageBuilder\Filament\Resources\PageTemplateResource;

class ListPageTemplates extends ListRecordsWithCreate
{
    protected static string $resource = PageTemplateResource::class;
}
