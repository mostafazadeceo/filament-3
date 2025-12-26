<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\ApiKeyResource\Pages;

use Filamat\IamSuite\Filament\Resources\ApiKeyResource;
use Filamat\IamSuite\Services\ApiKeyService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateApiKey extends CreateRecord
{
    protected static string $resource = ApiKeyResource::class;

    protected ?string $plainToken = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $result = app(ApiKeyService::class)->create($data);
        $this->plainToken = $result['token'];

        return $result['model'];
    }

    protected function afterCreate(): void
    {
        if ($this->plainToken) {
            Notification::make()
                ->title('کلید ای‌پی‌آی ساخته شد')
                ->body('کلید شما: '.$this->plainToken)
                ->persistent()
                ->success()
                ->send();
        }
    }
}
