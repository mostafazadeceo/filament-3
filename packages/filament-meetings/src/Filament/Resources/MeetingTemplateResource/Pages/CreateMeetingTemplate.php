<?php

namespace Haida\FilamentMeetings\Filament\Resources\MeetingTemplateResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentMeetings\Filament\Resources\MeetingTemplateResource;

class CreateMeetingTemplate extends CreateRecord
{
    protected static string $resource = MeetingTemplateResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['scope'] ?? 'workspace') === 'personal') {
            $data['owner_id'] = auth()->id();
        } else {
            $data['owner_id'] = null;
        }

        return $data;
    }
}
