<?php

namespace Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\CustomFieldManager;
use Haida\FilamentWorkhub\Services\WorkItemCreator;

class CreateWorkItem extends CreateRecord
{
    protected static string $resource = WorkItemResource::class;

    protected array $customFields = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $userId = auth()->id();
        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;
        $this->customFields = $data['custom_fields'] ?? [];
        unset($data['custom_fields']);

        return $data;
    }

    protected function handleRecordCreation(array $data): WorkItem
    {
        return app(WorkItemCreator::class)->create($data);
    }

    protected function afterCreate(): void
    {
        if ($this->customFields === []) {
            return;
        }

        app(CustomFieldManager::class)->syncForWorkItem($this->record, $this->customFields);
    }
}
