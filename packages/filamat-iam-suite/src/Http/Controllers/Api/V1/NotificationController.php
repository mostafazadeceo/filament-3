<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Services\NotificationService;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController
{
    public function __construct(protected NotificationService $service) {}

    public function send(Request $request): Response
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer'],
            'type' => ['required', 'string'],
            'payload' => ['nullable', 'array'],
        ]);

        $user = (config('auth.providers.users.model'))::query()->findOrFail($data['user_id']);
        $tenant = TenantContext::getTenant();
        if ($tenant && method_exists($user, 'tenants') && ! $user->tenants()->where('tenants.id', $tenant->getKey())->exists()) {
            return response(['message' => 'کاربر عضو این فضا نیست.'], 403);
        }

        $notification = $this->service->sendNotification($user, $data['type'], $data['payload'] ?? []);

        return response(['data' => $notification], 202);
    }
}
