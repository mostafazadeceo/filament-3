<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Services;

use Haida\MailtrapCore\Models\MailtrapConnection;

class MailtrapSendService
{
    public function __construct(
        protected MailtrapConnectionService $connections,
    ) {}

    /**
     * @param array<string, mixed> $options
     */
    public function sendSimple(MailtrapConnection $connection, string $toEmail, string $subject, string $body, array $options = []): array
    {
        $sandboxInboxId = (int) ($options['sandbox_inbox_id'] ?? 0);
        if ($sandboxInboxId > 0) {
            return $this->sendSandbox($connection, $sandboxInboxId, $toEmail, $subject, $body, $options);
        }

        $fromEmail = (string) ($options['from_email'] ?? config('filament-notify-mailtrap.default_from_address') ?? 'hello@example.com');
        $fromName = (string) ($options['from_name'] ?? config('filament-notify-mailtrap.default_from_name') ?? 'Mailtrap');

        $to = [
            'email' => $toEmail,
        ];
        if (! empty($options['to_name'])) {
            $to['name'] = $options['to_name'];
        }

        $payload = [
            'from' => [
                'email' => $fromEmail,
                'name' => $fromName,
            ],
            'to' => [
                $to,
            ],
            'subject' => $subject,
            'text' => $options['text'] ?? $body,
            'html' => $options['html'] ?? null,
            'category' => $options['category'] ?? 'Mailtrap Notification',
        ];

        if (! empty($options['html'])) {
            $payload['html'] = $options['html'];
        }

        if (! empty($options['custom_variables'])) {
            $payload['custom_variables'] = $options['custom_variables'];
        }

        return $this->connections->sendClient($connection)->sendEmail($payload);
    }

    /**
     * @param array<string, mixed> $options
     */
    protected function sendSandbox(MailtrapConnection $connection, int $inboxId, string $toEmail, string $subject, string $body, array $options = []): array
    {
        $fromEmail = (string) ($options['from_email'] ?? config('filament-notify-mailtrap.default_from_address') ?? 'hello@example.com');
        $fromName = (string) ($options['from_name'] ?? config('filament-notify-mailtrap.default_from_name') ?? 'Mailtrap');

        $to = [
            'email' => $toEmail,
        ];
        if (! empty($options['to_name'])) {
            $to['name'] = $options['to_name'];
        }

        $payload = [
            'from' => [
                'email' => $fromEmail,
                'name' => $fromName,
            ],
            'to' => [
                $to,
            ],
            'subject' => $subject,
            'text' => $options['text'] ?? $body,
            'html' => $options['html'] ?? null,
            'category' => $options['category'] ?? 'Mailtrap Sandbox',
        ];

        if (! empty($options['html'])) {
            $payload['html'] = $options['html'];
        }

        if (! empty($options['custom_variables'])) {
            $payload['custom_variables'] = $options['custom_variables'];
        }

        return $this->connections->sandboxSendClient($connection)->sendEmail($inboxId, $payload);
    }
}
