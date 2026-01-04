<?php

namespace Haida\Blog\Filament\Resources\BlogCategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\Blog\Filament\Resources\BlogCategoryResource;

class CreateBlogCategory extends CreateRecord
{
    protected static string $resource = BlogCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
