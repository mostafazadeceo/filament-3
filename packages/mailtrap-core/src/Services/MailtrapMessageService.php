<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Services;

use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapInbox;
use Haida\MailtrapCore\Models\MailtrapMessage;

class MailtrapMessageService
{
    public function __construct(
        protected MailtrapConnectionService $connections,
    ) {}

    /**
     * @return array<int, MailtrapMessage>
     */
    public function syncMessages(MailtrapConnection $connection, MailtrapInbox $inbox, array $params = []): array
    {
        $accountId = $this->connections->resolveAccountId($connection);
        if (! $accountId) {
            return [];
        }

        $client = $this->connections->client($connection);
        $messagesResponse = $client->listMessages($accountId, (int) $inbox->inbox_id, $params);
        $messages = $messagesResponse['data'] ?? $messagesResponse['messages'] ?? $messagesResponse;

        $rows = [];
        foreach ($messages as $message) {
            $rows[] = MailtrapMessage::query()->updateOrCreate([
                'tenant_id' => $connection->tenant_id,
                'connection_id' => $connection->getKey(),
                'inbox_id' => $inbox->getKey(),
                'message_id' => (int) ($message['id'] ?? 0),
            ], [
                'subject' => $message['subject'] ?? null,
                'from_email' => $message['from_email'] ?? $message['from'] ?? null,
                'to_email' => $message['to_email'] ?? $message['to'] ?? null,
                'sent_at' => $message['sent_at'] ?? $message['created_at'] ?? null,
                'size' => $message['size'] ?? null,
                'is_read' => (bool) ($message['is_read'] ?? false),
                'attachments_count' => (int) ($message['attachments_count'] ?? 0),
                'raw' => $message,
                'metadata' => [
                    'html_path' => $message['html_path'] ?? null,
                    'txt_path' => $message['txt_path'] ?? null,
                ],
                'synced_at' => now(),
            ]);
        }

        return $rows;
    }

    public function refreshMessageDetails(MailtrapConnection $connection, MailtrapInbox $inbox, MailtrapMessage $message): MailtrapMessage
    {
        $accountId = $this->connections->resolveAccountId($connection);
        if (! $accountId) {
            return $message;
        }

        $client = $this->connections->client($connection);
        $messageData = $client->findMessage($accountId, (int) $inbox->inbox_id, (int) $message->message_id);

        $htmlBody = null;
        $textBody = null;

        try {
            $htmlBody = $client->getMessageBody($accountId, (int) $inbox->inbox_id, (int) $message->message_id, 'html');
        } catch (\Throwable) {
            $htmlBody = null;
        }

        try {
            $textBody = $client->getMessageBody($accountId, (int) $inbox->inbox_id, (int) $message->message_id, 'txt');
        } catch (\Throwable) {
            $textBody = null;
        }

        $attachments = [];
        try {
            $attachments = $client->listMessageAttachments($accountId, (int) $inbox->inbox_id, (int) $message->message_id);
        } catch (\Throwable) {
            $attachments = [];
        }

        $message->update([
            'subject' => $messageData['subject'] ?? $message->subject,
            'from_email' => $messageData['from_email'] ?? $message->from_email,
            'to_email' => $messageData['to_email'] ?? $message->to_email,
            'sent_at' => $messageData['sent_at'] ?? $message->sent_at,
            'size' => $messageData['size'] ?? $message->size,
            'is_read' => (bool) ($messageData['is_read'] ?? $message->is_read),
            'attachments_count' => (int) ($messageData['attachments_count'] ?? $message->attachments_count),
            'html_body' => $htmlBody ?? $message->html_body,
            'text_body' => $textBody ?? $message->text_body,
            'metadata' => array_merge($message->metadata ?? [], [
                'attachments' => $attachments,
            ]),
            'raw' => $messageData ?: $message->raw,
        ]);

        return $message->refresh();
    }
}
