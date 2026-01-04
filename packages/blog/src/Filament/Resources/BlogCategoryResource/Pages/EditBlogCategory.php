<?php

namespace Haida\Blog\Filament\Resources\BlogCategoryResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\Blog\Filament\Resources\BlogCategoryResource;

class EditBlogCategory extends EditRecord
{
    protected static string $resource = BlogCategoryResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
