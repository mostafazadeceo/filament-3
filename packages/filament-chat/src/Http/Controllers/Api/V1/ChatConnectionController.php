<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Http\Controllers\Api\V1;

use App\Models\User;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentChat\Models\ChatConnection;
use Haida\FilamentChat\Services\ChatConnectionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ChatConnectionController extends Controller
{
    use AuthorizesRequests;

    public function test(ChatConnection $connection, ChatConnectionService $service): JsonResponse
    {
        $this->authorize('view', $connection);

        $result = $service->testConnection($connection);

        return response()->json([
            'ok' => true,
            'result' => $result,
        ]);
    }

    public function sync(ChatConnection $connection, ChatConnectionService $service): JsonResponse
    {
        $this->authorize('update', $connection);

        $count = $service->syncUsers($connection);

        return response()->json([
            'ok' => true,
            'synced' => $count,
        ]);
    }

    public function syncUser(ChatConnection $connection, User $user, ChatConnectionService $service): JsonResponse
    {
        $this->authorize('update', $connection);

        $tenant = TenantContext::getTenant();
        if ($tenant && ! $tenant->users()->whereKey($user->getKey())->exists()) {
            return response()->json([
                'ok' => false,
                'message' => 'کاربر عضو این فضای کاری نیست.',
            ], 403);
        }

        $link = $service->syncUser($connection, $user);

        return response()->json([
            'ok' => true,
            'link_id' => $link->getKey(),
        ]);
    }
}
