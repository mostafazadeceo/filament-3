<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapOfferResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentMailtrap\Resources\MailtrapOfferResource;

class CreateMailtrapOffer extends CreateRecord
{
    protected static string $resource = MailtrapOfferResource::class;
}
