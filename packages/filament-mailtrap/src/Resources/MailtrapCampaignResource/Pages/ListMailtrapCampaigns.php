<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapCampaignResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMailtrap\Resources\MailtrapCampaignResource;

class ListMailtrapCampaigns extends ListRecordsWithCreate
{
    protected static string $resource = MailtrapCampaignResource::class;
}
