<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\JournalEntryResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Vendor\FilamentAccountingIr\Filament\Resources\JournalEntryResource;

class ListJournalEntries extends ListRecordsWithCreate
{
    protected static string $resource = JournalEntryResource::class;
}
