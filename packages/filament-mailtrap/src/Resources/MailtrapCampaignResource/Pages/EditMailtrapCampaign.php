<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapCampaignResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentMailtrap\Resources\MailtrapCampaignResource;

class EditMailtrapCampaign extends EditRecord
{
    protected static string $resource = MailtrapCampaignResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }
}
