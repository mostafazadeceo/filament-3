<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\PrivilegeRequestResource\Pages;

use Filamat\IamSuite\Filament\Resources\PrivilegeRequestResource;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\PrivilegeElevationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CreatePrivilegeRequest extends CreateRecord
{
    protected static string $resource = PrivilegeRequestResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $tenant = Tenant::query()->find($data['tenant_id'] ?? null);
        $userModel = config('auth.providers.users.model');
        $user = $userModel::query()->find($data['user_id'] ?? null);
        $role = Role::query()->find($data['role_id'] ?? null);

        if (! $tenant || ! $user || ! $role) {
            throw new \RuntimeException('اطلاعات کافی نیست.');
        }

        return app(PrivilegeElevationService::class)->request(
            $tenant,
            $user,
            $role,
            (int) ($data['requested_duration_minutes'] ?? 60),
            auth()->user(),
            (string) ($data['reason'] ?? ''),
            (string) ($data['ticket_id'] ?? ''),
            $data['request_expires_at'] ?? null
        );
    }
}
