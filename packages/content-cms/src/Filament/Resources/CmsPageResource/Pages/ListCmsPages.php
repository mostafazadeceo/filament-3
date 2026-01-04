<?php

namespace Haida\ContentCms\Filament\Resources\CmsPageResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\ContentCms\Filament\Resources\CmsPageResource;

class ListCmsPages extends ListRecordsWithCreate
{
    protected static string $resource = CmsPageResource::class;
}
