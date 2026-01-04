<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Services;

use Haida\FilamentMailOps\Models\MailMailbox;
use Haida\FilamentMailOps\Models\MailOutboundMessage;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class MailSender
{
    public function sendAndLog(MailMailbox $mailbox, array $data): MailOutboundMessage
    {
        $storeBody = (bool) config('filament-mailops.outbound.store_body', true);

        $record = MailOutboundMessage::query()->create([
            'tenant_id' => $mailbox->tenant_id,
            'domain_id' => $mailbox->domain_id,
            'mailbox_id' => $mailbox->getKey(),
            'from_email' => $mailbox->email,
            'to_emails' => $this->normalizeEmails($data['to_emails'] ?? []),
            'cc_emails' => $this->normalizeEmails($data['cc_emails'] ?? []),
            'bcc_emails' => $this->normalizeEmails($data['bcc_emails'] ?? []),
            'subject' => $data['subject'] ?? null,
            'html_body' => $storeBody ? ($data['html_body'] ?? null) : null,
            'text_body' => $storeBody ? ($data['text_body'] ?? null) : null,
            'status' => 'pending',
        ]);

        try {
            $this->sendViaSmtp($mailbox, $data);

            $record->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (Throwable $exception) {
            $record->update([
                'status' => 'failed',
                'error_message' => Str::limit($exception->getMessage(), 500),
            ]);
        }

        return $record->refresh();
    }

    protected function sendViaSmtp(MailMailbox $mailbox, array $data): void
    {
        $settings = $mailbox->settings ?? [];

        $host = $settings['smtp_host'] ?? config('filament-mailops.smtp.host');
        $port = (int) ($settings['smtp_port'] ?? config('filament-mailops.smtp.port', 587));
        $encryption = $settings['smtp_encryption'] ?? config('filament-mailops.smtp.encryption', 'tls');
        if ($encryption === 'none') {
            $encryption = null;
        }

        config([
            'mail.mailers.mailops' => [
                'transport' => 'smtp',
                'host' => $host,
                'port' => $port,
                'encryption' => $encryption,
                'username' => $mailbox->email,
                'password' => $mailbox->password,
                'timeout' => 15,
            ],
        ]);

        $to = $this->normalizeEmails($data['to_emails'] ?? []);
        if ($to === []) {
            throw new \RuntimeException('Recipients are required.');
        }

        $cc = $this->normalizeEmails($data['cc_emails'] ?? []);
        $bcc = $this->normalizeEmails($data['bcc_emails'] ?? []);

        $subject = $data['subject'] ?? null;
        $html = $data['html_body'] ?? null;
        $text = $data['text_body'] ?? null;

        $fromName = $mailbox->display_name ?: config('filament-mailops.from.name');

        Mail::mailer('mailops')->send([], [], function (Message $message) use ($mailbox, $fromName, $to, $cc, $bcc, $subject, $html, $text) {
            $message->from($mailbox->email, $fromName ?: null);
            $message->to($to);

            if ($cc !== []) {
                $message->cc($cc);
            }

            if ($bcc !== []) {
                $message->bcc($bcc);
            }

            if ($subject) {
                $message->subject($subject);
            }

            if ($html) {
                $message->html($html);
            }

            if ($text) {
                $message->text($text);
            }
        });
    }

    /**
     * @param  array<int, string>|string|null  $emails
     * @return array<int, string>
     */
    protected function normalizeEmails(array|string|null $emails): array
    {
        if (is_string($emails)) {
            $emails = array_map('trim', explode(',', $emails));
        }

        if (! is_array($emails)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', $emails)));
    }
}
