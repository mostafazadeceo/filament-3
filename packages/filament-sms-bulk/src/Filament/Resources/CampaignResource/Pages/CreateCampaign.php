<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources\CampaignResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\SmsBulk\Filament\Resources\CampaignResource;

class CreateCampaign extends CreateRecord
{
    protected static string $resource = CampaignResource::class;
}
