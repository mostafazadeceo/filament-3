<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\ApiKeyResource\Pages;

use Filamat\IamSuite\Filament\Resources\ApiKeyResource;
use Filamat\IamSuite\Services\ApiKeyService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditApiKey extends EditRecord
{
    protected static string $resource = ApiKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('rotate')
                ->label('چرخش کلید')
                ->requiresConfirmation()
                ->action(function () {
                    $token = app(ApiKeyService::class)->rotate($this->record);

                    Notification::make()
                        ->title('کلید جدید ایجاد شد')
                        ->body('کلید شما: '.$token)
                        ->persistent()
                        ->success()
                        ->send();
                }),
        ];
    }
}
