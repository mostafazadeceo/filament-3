<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapAudienceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentMailtrap\Resources\MailtrapAudienceResource;

class CreateMailtrapAudience extends CreateRecord
{
    protected static string $resource = MailtrapAudienceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
