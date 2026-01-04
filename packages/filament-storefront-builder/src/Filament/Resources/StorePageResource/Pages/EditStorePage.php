<?php

namespace Haida\FilamentStorefrontBuilder\Filament\Resources\StorePageResource\Pages;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Haida\FilamentStorefrontBuilder\Filament\Resources\StorePageResource;
use Haida\FilamentStorefrontBuilder\Services\StorefrontPublishService;

class EditStorePage extends EditRecord
{
    protected static string $resource = StorePageResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_user_id'] = auth()->id();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        $actions = parent::getHeaderActions();

        $actions[] = Action::make('publish')
            ->label('انتشار')
            ->visible(fn () => IamAuthorization::allows('storebuilder.publish', IamAuthorization::resolveTenantFromRecord($this->record)))
            ->action(function (): void {
                app(StorefrontPublishService::class)->publish($this->record, auth()->user());
                $this->refreshFormData(['status', 'published_at']);
            });

        return $actions;
    }
}
