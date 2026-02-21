<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Services;

use Haida\FilamentMailOps\Models\MailInboundMessage;
use Haida\FilamentMailOps\Models\MailMailbox;
use Illuminate\Support\Arr;
use RuntimeException;

class ImapInboxReader
{
    public function isAvailable(): bool
    {
        return function_exists('imap_open');
    }

    public function sync(MailMailbox $mailbox, ?int $limit = null): int
    {
        if (! $this->isAvailable()) {
            throw new RuntimeException('PHP IMAP extension is not installed.');
        }

        $settings = $mailbox->settings ?? [];
        $host = $settings['imap_host'] ?? config('filament-mailops.imap.host');
        $port = (int) ($settings['imap_port'] ?? config('filament-mailops.imap.port', 993));
        $encryption = $settings['imap_encryption'] ?? config('filament-mailops.imap.encryption', 'ssl');
        $verifyTls = (bool) ($settings['imap_verify_tls'] ?? config('filament-mailops.imap.verify_tls', true));

        $flags = '/imap';
        if ($encryption === 'ssl') {
            $flags .= '/ssl';
        } elseif ($encryption === 'tls') {
            $flags .= '/tls';
        }

        if (! $verifyTls) {
            $flags .= '/novalidate-cert';
        }

        $mailboxString = sprintf('{%s:%d%s}INBOX', $host, $port, $flags);

        $imap = @imap_open($mailboxString, $mailbox->email, $mailbox->password);
        if (! $imap) {
            $error = imap_last_error() ?: 'IMAP connection failed.';

            // Prevent IMAP extension buffered warnings from surfacing on shutdown.
            if (function_exists('imap_errors')) {
                imap_errors();
            }
            if (function_exists('imap_alerts')) {
                imap_alerts();
            }

            throw new RuntimeException($error);
        }

        $ids = imap_search($imap, 'ALL') ?: [];
        $limit = $limit ?? (int) config('filament-mailops.inbound.sync_limit', 50);
        $ids = array_slice($ids, -$limit);

        $storeBody = (bool) config('filament-mailops.inbound.store_body', true);
        $count = 0;

        foreach ($ids as $msgNo) {
            $overview = imap_fetch_overview($imap, (string) $msgNo, 0);
            $overview = $overview[0] ?? null;
            if (! $overview) {
                continue;
            }

            $uid = (string) imap_uid($imap, (int) $msgNo);
            if ($uid === '0') {
                continue;
            }

            $headersRaw = imap_fetchheader($imap, (int) $msgNo) ?: '';
            $parsedHeaders = @imap_rfc822_parse_headers($headersRaw);

            $subject = isset($overview->subject) ? imap_utf8((string) $overview->subject) : null;
            $fromEmail = $this->parseFirstAddress($overview->from ?? null);
            $toEmails = $this->parseAddressList($overview->to ?? null);
            $ccEmails = $this->parseAddressList($overview->cc ?? null);
            $bccEmails = $this->parseAddressList($overview->bcc ?? null);

            $receivedAt = null;
            if (! empty($overview->date)) {
                try {
                    $receivedAt = \Carbon\Carbon::parse($overview->date);
                } catch (\Throwable) {
                    $receivedAt = null;
                }
            }

            $textBody = null;
            $htmlBody = null;

            if ($storeBody) {
                $structure = imap_fetchstructure($imap, (int) $msgNo);
                if ($structure) {
                    $textBody = $this->extractBodyPart($imap, (int) $msgNo, $structure, 'plain');
                    $htmlBody = $this->extractBodyPart($imap, (int) $msgNo, $structure, 'html');

                    if ($textBody === null && $htmlBody === null) {
                        $fallback = imap_body($imap, (int) $msgNo, FT_PEEK) ?: null;
                        $decoded = $this->decodeBody($fallback, $structure->encoding ?? null);

                        if (strtoupper((string) ($structure->subtype ?? '')) === 'HTML') {
                            $htmlBody = $decoded;
                        } else {
                            $textBody = $decoded;
                        }
                    }
                }
            }

            $messageId = $overview->message_id ?? ($parsedHeaders->message_id ?? null);

            MailInboundMessage::query()->updateOrCreate([
                'mailbox_id' => $mailbox->getKey(),
                'message_uid' => $uid,
            ], [
                'tenant_id' => $mailbox->tenant_id,
                'domain_id' => $mailbox->domain_id,
                'message_id' => $messageId,
                'subject' => $subject,
                'from_email' => $fromEmail,
                'to_emails' => $toEmails,
                'cc_emails' => $ccEmails,
                'bcc_emails' => $bccEmails,
                'received_at' => $receivedAt,
                'size' => $overview->size ?? null,
                'is_seen' => (bool) ($overview->seen ?? false),
                'text_body' => $textBody,
                'html_body' => $htmlBody,
                'raw_headers' => $headersRaw ? ['raw' => $headersRaw] : null,
                'metadata' => [
                    'imap_uid' => $uid,
                    'flags' => Arr::wrap($overview->flags ?? null),
                ],
                'synced_at' => now(),
            ]);

            $count++;
        }

        imap_close($imap);

        return $count;
    }

    protected function parseFirstAddress(?string $raw): ?string
    {
        $list = $this->parseAddressList($raw);

        return $list[0] ?? null;
    }

    /**
     * @return array<int, string>
     */
    protected function parseAddressList(?string $raw): array
    {
        if (! $raw) {
            return [];
        }

        $addresses = @imap_rfc822_parse_adrlist($raw, '');
        if (! is_array($addresses)) {
            return [];
        }

        $emails = [];
        foreach ($addresses as $address) {
            if (! isset($address->mailbox, $address->host)) {
                continue;
            }

            $email = $address->mailbox.'@'.$address->host;
            if ($email && $email !== '@') {
                $emails[] = $email;
            }
        }

        return array_values(array_unique($emails));
    }

    protected function extractBodyPart($imap, int $msgNo, object $structure, string $subtype, string $partNumber = ''): ?string
    {
        $target = strtoupper($subtype);

        if (! empty($structure->parts)) {
            foreach ($structure->parts as $index => $part) {
                $currentPart = $partNumber === '' ? (string) ($index + 1) : $partNumber.'.'.($index + 1);

                if (isset($part->type, $part->subtype) && (int) $part->type === 0 && strtoupper((string) $part->subtype) === $target) {
                    $body = imap_fetchbody($imap, $msgNo, $currentPart, FT_PEEK) ?: null;

                    return $this->decodeBody($body, $part->encoding ?? null);
                }

                if (! empty($part->parts)) {
                    $found = $this->extractBodyPart($imap, $msgNo, $part, $subtype, $currentPart);
                    if ($found !== null) {
                        return $found;
                    }
                }
            }
        } elseif (isset($structure->type, $structure->subtype) && (int) $structure->type === 0) {
            if (strtoupper((string) ($structure->subtype ?? '')) === $target) {
                $body = imap_body($imap, $msgNo, FT_PEEK) ?: null;

                return $this->decodeBody($body, $structure->encoding ?? null);
            }
        }

        return null;
    }

    protected function decodeBody(?string $body, mixed $encoding): ?string
    {
        if ($body === null) {
            return null;
        }

        return match ((int) $encoding) {
            3 => base64_decode($body) ?: $body,
            4 => quoted_printable_decode($body),
            default => $body,
        };
    }
}
