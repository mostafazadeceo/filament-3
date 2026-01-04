<?php

namespace Haida\Blog\Filament\Resources\BlogPostResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\Blog\Filament\Resources\BlogPostResource;

class CreateBlogPost extends CreateRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = $data['status'] ?? 'draft';
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
