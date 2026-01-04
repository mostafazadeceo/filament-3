<?php

namespace Haida\Blog\Filament\Resources\BlogCategoryResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\Blog\Filament\Resources\BlogCategoryResource;

class ListBlogCategories extends ListRecordsWithCreate
{
    protected static string $resource = BlogCategoryResource::class;
}
