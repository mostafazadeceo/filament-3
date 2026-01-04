<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAppApi\Models\AppSignalingMessage;
use Illuminate\Http\Request;

class RealtimeSignalController
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

        $query = AppSignalingMessage::query()
            ->where('tenant_id', TenantContext::getTenantId())
            ->where('to_user_id', $user->getKey());

        if ($request->filled('channel')) {
            $query->where('channel', $request->query('channel'));
        }

        $messages = $query->orderBy('created_at')->limit(100)->get();

        if ($messages->isNotEmpty()) {
            AppSignalingMessage::query()->whereIn('id', $messages->pluck('id'))->delete();
        }

        return response()->json(['data' => $messages]);
    }

    public function store(Request $request)
    {
        if (! TenantContext::getTenantId()) {
            return response()->json(['message' => 'فضای کاری مشخص نیست.'], 422);
        }

        $data = $request->validate([
            'to_user_id' => ['nullable', 'integer'],
            'channel' => ['nullable', 'string', 'max:128'],
            'payload' => ['required', 'array'],
        ]);

        $message = AppSignalingMessage::create([
            'tenant_id' => TenantContext::getTenantId(),
            'from_user_id' => $request->user()?->getKey(),
            'to_user_id' => $data['to_user_id'] ?? null,
            'channel' => $data['channel'] ?? null,
            'payload' => $data['payload'],
        ]);

        return response()->json(['data' => $message], 201);
    }
}
