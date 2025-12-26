<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserResource\Pages;

use Filamat\IamSuite\Filament\Resources\UserResource;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $tenant = TenantContext::getTenant();
        if (! $tenant || ! method_exists($this->record, 'tenants')) {
            return;
        }

        $this->record->tenants()->syncWithoutDetaching([
            $tenant->getKey() => [
                'role' => 'member',
                'status' => 'active',
                'joined_at' => now(),
            ],
        ]);
    }
}
