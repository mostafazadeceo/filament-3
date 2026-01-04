<?php

namespace Haida\Blog\Filament\Resources\BlogPostResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\Blog\Filament\Resources\BlogPostResource;

class ListBlogPosts extends ListRecordsWithCreate
{
    protected static string $resource = BlogPostResource::class;
}
