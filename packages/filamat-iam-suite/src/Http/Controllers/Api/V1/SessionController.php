<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Http\Controllers\Api\V1\Concerns\ResolvesTenant;
use Filamat\IamSuite\Models\UserSession;
use Filamat\IamSuite\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SessionController
{
    use ResolvesTenant;

    public function index(Request $request): Response
    {
        $query = UserSession::query();

        $tenant = $this->resolveTenant($request);
        if ($response = $this->ensureTenantRequest($request, $tenant)) {
            return $response;
        }
        if ($tenant) {
            $query->where('tenant_id', $tenant->getKey());
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        $sessions = $query->orderByDesc('last_activity_at')->paginate(20);

        return response(['data' => $sessions], 200);
    }

    public function revoke(UserSession $session, Request $request): Response
    {
        $tenant = $this->resolveTenant($request);
        if ($response = $this->ensureTenantRequest($request, $tenant)) {
            return $response;
        }
        if ($tenant && (int) $session->tenant_id !== (int) $tenant->getKey()) {
            return response(['message' => 'نشست یافت نشد.'], 404);
        }

        $data = $request->validate([
            'reason' => 'required|string',
        ]);

        app(SessionService::class)->revoke($session, $request->user(), (string) $data['reason']);

        return response(['data' => $session->fresh()], 200);
    }
}
