<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapAudienceResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentMailtrap\Resources\MailtrapAudienceResource;

class EditMailtrapAudience extends EditRecord
{
    protected static string $resource = MailtrapAudienceResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
