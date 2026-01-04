<?php

namespace Haida\Blog\Filament\Resources\BlogPostResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\Blog\Filament\Resources\BlogPostResource;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
