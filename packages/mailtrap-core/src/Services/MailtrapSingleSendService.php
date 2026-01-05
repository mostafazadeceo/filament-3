<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Services;

use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapSingleSend;
use Throwable;

class MailtrapSingleSendService
{
    public function __construct(
        protected MailtrapSendService $sendService,
    ) {}

    /**
     * @param  array<string, mixed>  $options
     */
    public function sendAndLog(MailtrapConnection $connection, string $toEmail, string $subject, string $body, array $options = []): MailtrapSingleSend
    {
        $record = MailtrapSingleSend::query()->create([
            'tenant_id' => $connection->tenant_id,
            'connection_id' => $connection->getKey(),
            'to_email' => $toEmail,
            'to_name' => $options['to_name'] ?? null,
            'subject' => $subject,
            'html_body' => $options['html'] ?? null,
            'text_body' => $options['text'] ?? $body,
            'status' => 'pending',
            'created_by_user_id' => $options['created_by_user_id'] ?? null,
        ]);

        try {
            $response = $this->sendService->sendSimple($connection, $toEmail, $subject, $body, $options);

            $record->update([
                'status' => 'sent',
                'response' => $response,
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (Throwable $exception) {
            $record->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'sent_at' => now(),
            ]);
        }

        return $record->refresh();
    }
}
