<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\CampaignRecipientResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Haida\SmsBulk\Filament\Resources\CampaignRecipientResource;

class ListCampaignRecipients extends ListRecords
{
    protected static string $resource = CampaignRecipientResource::class;
}
