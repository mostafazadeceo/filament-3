<?php

namespace Haida\ContentCms\Filament\Resources\CmsPageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\ContentCms\Filament\Resources\CmsPageResource;

class CreateCmsPage extends CreateRecord
{
    protected static string $resource = CmsPageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = $data['status'] ?? 'draft';
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
