<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAppApi\Models\AppSupportMessage;
use Haida\FilamentAppApi\Models\AppSupportTicket;

class SupportService
{
    public function __construct(private readonly SyncService $syncService) {}

    public function createTicket(string $subject, string $priority, ?int $userId = null): AppSupportTicket
    {
        $ticket = AppSupportTicket::create([
            'tenant_id' => TenantContext::getTenantId(),
            'user_id' => $userId,
            'subject' => $subject,
            'priority' => $priority,
            'status' => 'open',
            'latest_message_at' => now(),
        ]);

        $this->syncService->recordChange('support', 'ticket', (string) $ticket->getKey(), 'upsert', [
            'subject' => $ticket->subject,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
        ]);

        return $ticket;
    }

    public function addMessage(AppSupportTicket $ticket, ?int $userId, ?string $body, string $type, ?string $attachmentPath = null, array $meta = []): AppSupportMessage
    {
        $message = AppSupportMessage::create([
            'tenant_id' => $ticket->tenant_id,
            'ticket_id' => $ticket->getKey(),
            'user_id' => $userId,
            'body' => $body,
            'type' => $type,
            'attachment_path' => $attachmentPath,
            'meta' => $meta,
        ]);

        $ticket->forceFill(['latest_message_at' => now()])->save();

        $this->syncService->recordChange('support', 'message', (string) $message->getKey(), 'upsert', [
            'ticket_id' => $ticket->getKey(),
            'type' => $type,
        ]);

        return $message;
    }
}
