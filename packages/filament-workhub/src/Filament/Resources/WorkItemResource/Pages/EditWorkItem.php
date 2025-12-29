<?php

namespace Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentWorkhub\Filament\Resources\WorkItemResource;
use Haida\FilamentWorkhub\Services\CustomFieldManager;

class EditWorkItem extends EditRecord
{
    protected static string $resource = WorkItemResource::class;

    protected array $customFields = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = parent::mutateFormDataBeforeFill($data);

        $values = $this->record
            ->customFieldValues()
            ->with('field')
            ->get()
            ->mapWithKeys(fn ($value) => [$value->field?->key => $value->value])
            ->toArray();

        $data['custom_fields'] = $values;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();
        $this->customFields = $data['custom_fields'] ?? [];
        unset($data['custom_fields']);

        return $data;
    }

    protected function afterSave(): void
    {
        app(CustomFieldManager::class)->syncForWorkItem($this->record, $this->customFields);
    }
}
