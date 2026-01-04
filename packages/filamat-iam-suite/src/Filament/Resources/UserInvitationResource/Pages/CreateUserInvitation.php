<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserInvitationResource\Pages;

use Filamat\IamSuite\Filament\Resources\UserInvitationResource;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\InviteUserService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUserInvitation extends CreateRecord
{
    protected static string $resource = UserInvitationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $tenant = ! empty($data['tenant_id'])
            ? Tenant::query()->find($data['tenant_id'])
            : null;

        if (! $tenant) {
            throw new \RuntimeException('فضای کاری یافت نشد.');
        }

        $result = app(InviteUserService::class)->invite(
            $tenant,
            (string) ($data['email'] ?? ''),
            (string) ($data['name'] ?? ''),
            [],
            [],
            auth()->user(),
            (string) ($data['reason'] ?? ''),
            $data['expires_at'] ?? null
        );

        return $result['invitation'];
    }
}
