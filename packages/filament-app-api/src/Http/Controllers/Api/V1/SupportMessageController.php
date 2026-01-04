<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAppApi\Models\AppSupportTicket;
use Haida\FilamentAppApi\Services\SupportService;
use Illuminate\Http\Request;

class SupportMessageController
{
    public function __construct(private readonly SupportService $supportService) {}

    public function index(AppSupportTicket $ticket)
    {
        if (! TenantContext::getTenantId()) {
            return response()->json(['message' => 'فضای کاری مشخص نیست.'], 422);
        }

        if ($ticket->tenant_id !== TenantContext::getTenantId()) {
            return response()->json(['message' => 'تیکت معتبر نیست.'], 403);
        }

        $messages = $ticket->messages()->orderBy('created_at')->limit(200)->get();

        return response()->json(['data' => $messages]);
    }

    public function store(Request $request, AppSupportTicket $ticket)
    {
        if (! TenantContext::getTenantId()) {
            return response()->json(['message' => 'فضای کاری مشخص نیست.'], 422);
        }

        if ($ticket->tenant_id !== TenantContext::getTenantId()) {
            return response()->json(['message' => 'تیکت معتبر نیست.'], 403);
        }

        $data = $request->validate([
            'body' => ['required', 'string'],
            'type' => ['nullable', 'string', 'max:32'],
        ]);

        $message = $this->supportService->addMessage(
            $ticket,
            $request->user()?->getKey(),
            $data['body'],
            $data['type'] ?? 'text'
        );

        return response()->json(['data' => $message], 201);
    }
}
