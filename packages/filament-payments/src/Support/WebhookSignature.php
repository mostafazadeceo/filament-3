<?php

namespace Haida\FilamentPayments\Support;

class WebhookSignature
{
    /**
     * @param  array<string, mixed>  $headers
     */
    public function verify(string $payload, array $headers, string $secret): bool
    {
        $signatureHeader = (string) config('filament-payments.webhooks.signature_header', 'X-Signature');
        $timestampHeader = (string) config('filament-payments.webhooks.timestamp_header', 'X-Timestamp');
        $tolerance = (int) config('filament-payments.webhooks.tolerance_seconds', 300);

        $signature = $this->headerValue($headers, $signatureHeader);
        $timestamp = $this->headerValue($headers, $timestampHeader);

        if (! $signature || ! $timestamp || ! ctype_digit((string) $timestamp)) {
            return false;
        }

        $timestampValue = (int) $timestamp;
        if (abs(time() - $timestampValue) > $tolerance) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestampValue.'.'.$payload, $secret);

        return hash_equals($expected, (string) $signature);
    }

    /**
     * @param  array<string, mixed>  $headers
     */
    private function headerValue(array $headers, string $key): ?string
    {
        foreach ($headers as $header => $value) {
            if (strcasecmp($header, $key) === 0) {
                if (is_array($value)) {
                    return (string) ($value[0] ?? null);
                }

                return (string) $value;
            }
        }

        return null;
    }
}
