<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\Notification as IamNotification;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\Request;

class NotificationFeedController
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['data' => []]);
        }

        if (! TenantContext::getTenantId()) {
            return response()->json(['message' => 'فضای کاری مشخص نیست.'], 422);
        }

        $notifications = IamNotification::query()
            ->where('tenant_id', TenantContext::getTenantId())
            ->where('user_id', $user->getKey())
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json(['data' => $notifications]);
    }

    public function markRead(Request $request, IamNotification $notification)
    {
        $user = $request->user();
        if (! $user || $notification->user_id !== $user->getKey()) {
            return response()->json(['message' => 'دسترسی کافی نیست.'], 403);
        }

        if (! TenantContext::getTenantId()) {
            return response()->json(['message' => 'فضای کاری مشخص نیست.'], 422);
        }

        if ($notification->tenant_id !== TenantContext::getTenantId()) {
            return response()->json(['message' => 'فضای کاری معتبر نیست.'], 403);
        }

        $notification->forceFill(['status' => 'read'])->save();

        return response()->json(['message' => 'خوانده شد.']);
    }
}
