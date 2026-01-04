<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\MailtrapCore\Http\Resources\MailtrapMessageResource;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapInbox;
use Haida\MailtrapCore\Models\MailtrapMessage;
use Haida\MailtrapCore\Services\MailtrapConnectionService;
use Haida\MailtrapCore\Services\MailtrapMessageService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessageController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(MailtrapMessage::class, 'message');
    }

    public function index(): AnonymousResourceCollection
    {
        $query = MailtrapMessage::query()->latest();

        if ($inboxId = request('inbox_id')) {
            $query->where('inbox_id', $inboxId);
        }

        if ($connectionId = request('connection_id')) {
            $query->where('connection_id', $connectionId);
        }

        return MailtrapMessageResource::collection($query->paginate());
    }

    public function show(MailtrapMessage $message): MailtrapMessageResource
    {
        return new MailtrapMessageResource($message);
    }

    public function body(MailtrapMessage $message, MailtrapMessageService $service): array
    {
        $message = $this->refreshIfRequested($message, $service);

        return [
            'html' => $message->html_body,
            'text' => $message->text_body,
        ];
    }

    public function attachments(MailtrapMessage $message, MailtrapMessageService $service): array
    {
        $message = $this->refreshIfRequested($message, $service);

        return [
            'attachments' => data_get($message->metadata, 'attachments', []),
        ];
    }

    public function downloadAttachment(MailtrapMessage $message, int $attachment, MailtrapMessageService $service): StreamedResponse
    {
        $message = $this->refreshIfRequested($message, $service);

        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            abort(404);
        }

        $connection = MailtrapConnection::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('id', $message->connection_id)
            ->firstOrFail();

        $accountId = $connection->account_id ?: app(MailtrapConnectionService::class)->resolveAccountId($connection);
        if (! $accountId) {
            abort(404);
        }

        $inbox = MailtrapInbox::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('id', $message->inbox_id)
            ->firstOrFail();

        $client = app(MailtrapConnectionService::class)->client($connection);
        $content = $client
            ->downloadAttachment((int) $accountId, (int) $inbox->inbox_id, (int) $message->message_id, $attachment);

        return response()->streamDownload(function () use ($content): void {
            echo $content;
        }, 'attachment-' . $attachment . '.bin');
    }

    protected function refreshIfRequested(MailtrapMessage $message, MailtrapMessageService $service): MailtrapMessage
    {
        if (! request()->boolean('refresh')) {
            return $message;
        }

        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            return $message;
        }

        $connection = MailtrapConnection::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('id', $message->connection_id)
            ->first();

        $inbox = MailtrapInbox::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('id', $message->inbox_id)
            ->first();

        if (! $connection || ! $inbox) {
            return $message;
        }

        return $service->refreshMessageDetails($connection, $inbox, $message);
    }
}
