<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\OrganizationResource\Pages;

use Filamat\IamSuite\Filament\Resources\OrganizationResource;
use Filamat\IamSuite\Services\OrganizationEntitlementService;
use Filament\Resources\Pages\CreateRecord;

class CreateOrganization extends CreateRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function afterCreate(): void
    {
        $record = $this->getRecord();
        if (! $record) {
            return;
        }

        $entitlements = (array) data_get($record->settings ?? [], 'entitlements', []);

        app(OrganizationEntitlementService::class)->updateEntitlements($record, $entitlements, auth()->user());
    }
}
