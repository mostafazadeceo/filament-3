<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Http\Controllers\Api\V1;

use Filamat\IamSuite\Http\Controllers\Api\V1\BaseController;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentMailOps\Models\MailMailbox;
use Haida\FilamentMailOps\Models\MailOutboundMessage;
use Haida\FilamentMailOps\Services\MailSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OutboundMessageController extends BaseController
{
    protected function modelClass(): string
    {
        return MailOutboundMessage::class;
    }

    protected function validationRules(string $action): array
    {
        $mailboxesTable = config('filament-mailops.tables.mailboxes', 'mailops_mailboxes');

        $mailboxRule = Rule::exists($mailboxesTable, 'id');
        if (! TenantContext::shouldBypass() && TenantContext::getTenantId()) {
            $mailboxRule = $mailboxRule->where('tenant_id', TenantContext::getTenantId());
        }

        return [
            'mailbox_id' => ['required', 'integer', $mailboxRule],
            'to_emails' => ['required', 'array', 'min:1'],
            'to_emails.*' => ['email'],
            'cc_emails' => ['nullable', 'array'],
            'cc_emails.*' => ['email'],
            'bcc_emails' => ['nullable', 'array'],
            'bcc_emails.*' => ['email'],
            'subject' => ['nullable', 'string', 'max:255'],
            'text_body' => ['nullable', 'string'],
            'html_body' => ['nullable', 'string'],
        ];
    }

    public function store(Request $request, ?int $parentId = null): \Illuminate\Http\Response
    {
        $validator = Validator::make($request->all(), $this->validationRules('store'));
        $validator->after(function ($validator) use ($request) {
            if (! $request->filled('text_body') && ! $request->filled('html_body')) {
                $validator->errors()->add('text_body', 'متن ایمیل الزامی است.');
            }
        });

        $data = $validator->validate();

        $mailbox = MailMailbox::query()->findOrFail($data['mailbox_id']);
        $record = app(MailSender::class)->sendAndLog($mailbox, $data);

        return response(['data' => $record], 201);
    }
}
