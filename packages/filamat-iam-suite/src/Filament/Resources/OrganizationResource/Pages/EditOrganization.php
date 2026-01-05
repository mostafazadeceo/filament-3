<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\OrganizationResource\Pages;

use Filamat\IamSuite\Filament\Resources\OrganizationResource;
use Filamat\IamSuite\Services\OrganizationEntitlementService;
use Filament\Resources\Pages\EditRecord;

class EditOrganization extends EditRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        if (! $record) {
            return;
        }

        $entitlements = (array) data_get($record->settings ?? [], 'entitlements', []);

        $service = app(OrganizationEntitlementService::class);
        $service->updateEntitlements($record, $entitlements, auth()->user());
        $service->syncOrganizationAccess($record, auth()->user());
    }
}
