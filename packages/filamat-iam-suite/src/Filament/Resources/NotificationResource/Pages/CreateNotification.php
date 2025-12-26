<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\NotificationResource\Pages;

use Filamat\IamSuite\Filament\Resources\NotificationResource;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\NotificationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateNotification extends CreateRecord
{
    protected static string $resource = NotificationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $tenant = null;
        if (! empty($data['tenant_id'])) {
            $tenant = Tenant::query()->find($data['tenant_id']);
        }

        $user = null;
        $userModel = config('auth.providers.users.model');
        if (! empty($data['user_id'])) {
            $user = $userModel::query()->find($data['user_id']);
        }

        $payload = is_array($data['payload'] ?? null) ? $data['payload'] : [];

        return app(NotificationService::class)->sendNotification(
            $user,
            (string) ($data['type'] ?? 'custom'),
            $payload,
            $tenant
        );
    }
}
