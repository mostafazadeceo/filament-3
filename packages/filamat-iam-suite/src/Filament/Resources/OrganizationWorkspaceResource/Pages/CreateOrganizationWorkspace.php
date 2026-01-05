<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\OrganizationWorkspaceResource\Pages;

use Filamat\IamSuite\Filament\Resources\OrganizationWorkspaceResource;
use Filamat\IamSuite\Services\TenantProvisioningService;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Auth\Authenticatable;

class CreateOrganizationWorkspace extends CreateRecord
{
    protected static string $resource = OrganizationWorkspaceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenant = TenantContext::getTenant();
        if ($tenant) {
            $data['organization_id'] = $tenant->organization_id;
        }

        if (! isset($data['status'])) {
            $data['status'] = 'active';
        }

        if (! isset($data['owner_user_id']) && auth()->check()) {
            $data['owner_user_id'] = auth()->id();
        }

        return $data;
    }

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
