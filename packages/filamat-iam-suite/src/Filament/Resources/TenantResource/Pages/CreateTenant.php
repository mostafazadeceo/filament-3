<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\TenantResource\Pages;

use Filamat\IamSuite\Filament\Resources\TenantResource;
use Filamat\IamSuite\Services\TenantProvisioningService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Auth\Authenticatable;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function afterCreate(): void
    {
        $record = $this->getRecord();
        if (! $record) {
            return;
        }

        $owner = $this->resolveOwner();
        if (! $owner) {
            return;
        }

        $data = $this->form->getState();
        $modules = (array) data_get($data, 'settings.access.modules', []);

        app(TenantProvisioningService::class)->finalizeTenant($record, $owner, $modules, auth()->user());
    }

    protected function resolveOwner(): ?Authenticatable
    {
        $record = $this->getRecord();
        if (! $record) {
            return null;
        }

        $userModel = config('auth.providers.users.model');

        return $userModel::query()->find($record->owner_user_id);
    }
}
