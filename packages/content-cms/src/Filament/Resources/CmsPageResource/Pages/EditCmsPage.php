<?php

namespace Haida\ContentCms\Filament\Resources\CmsPageResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\ContentCms\Filament\Resources\CmsPageResource;

class EditCmsPage extends EditRecord
{
    protected static string $resource = CmsPageResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
