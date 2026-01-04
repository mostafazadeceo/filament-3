<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\PrivilegeEligibilityResource\Pages;

use Filamat\IamSuite\Filament\Resources\PrivilegeEligibilityResource;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\PrivilegeEligibilityService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CreatePrivilegeEligibility extends CreateRecord
{
    protected static string $resource = PrivilegeEligibilityResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $tenant = Tenant::query()->find($data['tenant_id'] ?? null);
        $userModel = config('auth.providers.users.model');
        $user = $userModel::query()->find($data['user_id'] ?? null);
        $role = Role::query()->find($data['role_id'] ?? null);

        if (! $tenant || ! $user || ! $role) {
            throw new \RuntimeException('اطلاعات کافی نیست.');
        }

        $eligibility = app(PrivilegeEligibilityService::class)->grant(
            $tenant,
            $user,
            $role,
            auth()->user(),
            (string) ($data['reason'] ?? '')
        );

        $eligibility->update([
            'can_request' => (bool) ($data['can_request'] ?? true),
            'active' => (bool) ($data['active'] ?? true),
        ]);

        return $eligibility;
    }
}
