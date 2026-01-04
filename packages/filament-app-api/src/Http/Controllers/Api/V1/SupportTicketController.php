<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAppApi\Models\AppSupportTicket;
use Haida\FilamentAppApi\Services\SupportService;
use Illuminate\Http\Request;

class SupportTicketController
{
    public function __construct(private readonly SupportService $supportService) {}

    public function index(Request $request)
    {
        if (! TenantContext::getTenantId()) {
            return response()->json(['message' => 'فضای کاری مشخص نیست.'], 422);
        }

        $tickets = AppSupportTicket::query()
            ->where('tenant_id', TenantContext::getTenantId())
            ->orderByDesc('updated_at')
            ->limit(50)
            ->get();

        return response()->json(['data' => $tickets]);
    }

    public function store(Request $request)
    {
        if (! TenantContext::getTenantId()) {
            return response()->json(['message' => 'فضای کاری مشخص نیست.'], 422);
        }

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'priority' => ['nullable', 'string', 'max:32'],
        ]);

        $ticket = $this->supportService->createTicket(
            $data['subject'],
            $data['priority'] ?? 'normal',
            $request->user()?->getKey()
        );

        return response()->json(['data' => $ticket], 201);
    }
}
