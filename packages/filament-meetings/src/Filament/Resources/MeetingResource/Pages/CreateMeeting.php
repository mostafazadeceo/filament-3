<?php

namespace Haida\FilamentMeetings\Filament\Resources\MeetingResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentMeetings\Filament\Resources\MeetingResource;

class CreateMeeting extends CreateRecord
{
    protected static string $resource = MeetingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $userId = auth()->id();
        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;

        if (empty($data['organizer_id'])) {
            $data['organizer_id'] = $userId;
        }

        return $data;
    }
}
