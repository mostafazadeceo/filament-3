<?php

namespace Haida\FilamentMeetings\Filament\Resources\MeetingTemplateResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentMeetings\Filament\Resources\MeetingTemplateResource;

class EditMeetingTemplate extends EditRecord
{
    protected static string $resource = MeetingTemplateResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['scope'] ?? 'workspace') === 'personal') {
            $data['owner_id'] = $data['owner_id'] ?? auth()->id();
        } else {
            $data['owner_id'] = null;
        }

        return $data;
    }
}
