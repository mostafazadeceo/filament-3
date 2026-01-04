<?php

namespace Haida\FilamentMeetings\Filament\Resources\MeetingResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentMeetings\Filament\Resources\MeetingResource;

class EditMeeting extends EditRecord
{
    protected static string $resource = MeetingResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
