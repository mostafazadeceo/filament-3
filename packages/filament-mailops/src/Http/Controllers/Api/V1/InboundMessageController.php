<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Http\Controllers\Api\V1;

use Filamat\IamSuite\Http\Controllers\Api\V1\BaseController;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentMailOps\Models\MailInboundMessage;
use Haida\FilamentMailOps\Models\MailMailbox;
use Haida\FilamentMailOps\Services\ImapInboxReader;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InboundMessageController extends BaseController
{
    protected function modelClass(): string
    {
        return MailInboundMessage::class;
    }

    protected function validationRules(string $action): array
    {
        return [];
    }

    public function sync(Request $request, ImapInboxReader $reader): \Illuminate\Http\Response
    {
        if (! $reader->isAvailable()) {
            return response([
                'message' => 'IMAP sync is unavailable on this server.',
                'error' => 'php_imap_extension_missing',
            ], 503);
        }

        $mailboxesTable = config('filament-mailops.tables.mailboxes', 'mailops_mailboxes');

        $mailboxRule = Rule::exists($mailboxesTable, 'id');
        if (! TenantContext::shouldBypass() && TenantContext::getTenantId()) {
            $mailboxRule = $mailboxRule->where('tenant_id', TenantContext::getTenantId());
        }

        $data = $request->validate([
            'mailbox_id' => ['required', 'integer', $mailboxRule],
            'limit' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $mailbox = MailMailbox::query()->findOrFail($data['mailbox_id']);
        try {
            $count = $reader->sync($mailbox, $data['limit'] ?? null);
        } catch (\Throwable $exception) {
            return response([
                'message' => 'IMAP sync failed.',
                'error' => $exception->getMessage(),
            ], 422);
        }

        return response(['synced' => $count], 200);
    }
}
