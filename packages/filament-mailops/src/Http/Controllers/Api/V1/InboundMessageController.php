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
        $count = $reader->sync($mailbox, $data['limit'] ?? null);

        return response(['synced' => $count], 200);
    }
}
