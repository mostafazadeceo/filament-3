<?php

namespace Haida\Blog\Filament\Resources\BlogTagResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\Blog\Filament\Resources\BlogTagResource;

class CreateBlogTag extends CreateRecord
{
    protected static string $resource = BlogTagResource::class;
}
