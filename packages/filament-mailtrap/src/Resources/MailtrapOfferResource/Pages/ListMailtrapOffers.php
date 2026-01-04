<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapOfferResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\FilamentMailtrap\Resources\MailtrapOfferResource;

class ListMailtrapOffers extends ListRecordsWithCreate
{
    protected static string $resource = MailtrapOfferResource::class;
}
