<?php

namespace Haida\Blog\Filament\Resources\BlogTagResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\Blog\Filament\Resources\BlogTagResource;

class ListBlogTags extends ListRecordsWithCreate
{
    protected static string $resource = BlogTagResource::class;
}
