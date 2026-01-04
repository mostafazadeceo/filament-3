<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAppApi\Models\AppSupportTicket;
use Haida\FilamentAppApi\Services\SupportService;
use Illuminate\Http\Request;

class SupportAttachmentController
{
    public function __construct(private readonly SupportService $supportService) {}

    public function store(Request $request, AppSupportTicket $ticket)
    {
        if (! TenantContext::getTenantId()) {
            return response()->json(['message' => 'فضای کاری مشخص نیست.'], 422);
        }

        if ($ticket->tenant_id !== TenantContext::getTenantId()) {
            return response()->json(['message' => 'تیکت معتبر نیست.'], 403);
        }

        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'type' => ['nullable', 'string', 'max:32'],
        ]);

        $path = $request->file('file')->store('app-support');

        $message = $this->supportService->addMessage(
            $ticket,
            $request->user()?->getKey(),
            null,
            $data['type'] ?? 'attachment',
            $path,
            ['original_name' => $request->file('file')->getClientOriginalName()]
        );

        return response()->json(['data' => $message], 201);
    }
}
