<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Jobs;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Models\SmsBulkCampaign;
use Haida\SmsBulk\Models\SmsBulkCampaignRecipient;
use Haida\SmsBulk\Services\ProviderClientFactory;
use Haida\SmsBulk\Services\RoutingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\RateLimiter;

class SendCampaignChunkJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @param array<int, int> $recipientIds */
    public function __construct(
        public readonly int $tenantId,
        public readonly int $campaignId,
        public readonly array $recipientIds,
    ) {}

    public function handle(ProviderClientFactory $clients, RoutingService $routing): void
    {
        TenantContext::setTenant(Tenant::query()->find($this->tenantId));

        $campaign = SmsBulkCampaign::query()
            ->where('tenant_id', $this->tenantId)
            ->findOrFail($this->campaignId);

        if (in_array($campaign->status, ['paused', 'cancelled'], true)) {
            return;
        }

        $campaign->update(['status' => 'sending', 'started_at' => $campaign->started_at ?: now()]);

        $resolved = $routing->resolve($this->tenantId, $campaign->provider_connection_id);
        $primary = $resolved['primary'];
        $fallback = $resolved['fallback'];

        if (! $primary) {
            return;
        }

        $recipients = SmsBulkCampaignRecipient::query()
            ->where('tenant_id', $this->tenantId)
            ->where('campaign_id', $campaign->getKey())
            ->whereIn('id', $this->recipientIds)
            ->get();

        foreach ($recipients as $recipient) {
            if (! in_array($recipient->status, ['queued', 'failed'], true)) {
                continue;
            }

            $rateKey = 'sms-bulk:send:'.$this->tenantId;
            if (RateLimiter::tooManyAttempts($rateKey, 100)) {
                $this->release(2);

                return;
            }

            RateLimiter::hit($rateKey, 1);

            $payload = [
                'number' => $campaign->sender,
                'recipients' => [$recipient->msisdn],
                'message' => (string) (($campaign->payload_snapshot['message'] ?? '')),
                'schedule_at' => $campaign->schedule_at?->toIso8601String(),
            ];

            try {
                $response = $this->sendWithMode($clients->make($primary), $campaign->mode, $payload, $campaign->payload_snapshot ?? []);
                $recipient->update([
                    'status' => 'sent',
                    'remote_message_id' => (string) (($response['data']['id'] ?? '') ?: ($response['data']['bulk_id'] ?? '')),
                    'cost' => (float) (($response['data']['price'] ?? 0) ?: 0),
                    'error_code' => null,
                    'error_message' => null,
                ]);
            } catch (\Throwable $exception) {
                if ($fallback) {
                    try {
                        $response = $this->sendWithMode($clients->make($fallback), $campaign->mode, $payload, $campaign->payload_snapshot ?? []);
                        $recipient->update([
                            'status' => 'sent',
                            'remote_message_id' => (string) (($response['data']['id'] ?? '') ?: ($response['data']['bulk_id'] ?? '')),
                            'cost' => (float) (($response['data']['price'] ?? 0) ?: 0),
                            'error_code' => null,
                            'error_message' => null,
                        ]);

                        continue;
                    } catch (\Throwable $fallbackException) {
                        $recipient->update([
                            'status' => 'failed',
                            'error_code' => 'provider_error',
                            'error_message' => $fallbackException->getMessage(),
                        ]);

                        continue;
                    }
                }

                $recipient->update([
                    'status' => 'failed',
                    'error_code' => 'provider_error',
                    'error_message' => $exception->getMessage(),
                ]);
            }
        }

        $remaining = SmsBulkCampaignRecipient::query()
            ->where('tenant_id', $this->tenantId)
            ->where('campaign_id', $campaign->getKey())
            ->whereIn('status', ['queued', 'failed'])
            ->exists();

        if (! $remaining) {
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now(),
                'cost_final' => (float) SmsBulkCampaignRecipient::query()
                    ->where('tenant_id', $this->tenantId)
                    ->where('campaign_id', $campaign->getKey())
                    ->sum('cost'),
            ]);
        }
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $snapshot
     * @return array<string, mixed>
     */
    protected function sendWithMode(object $client, string $mode, array $payload, array $snapshot): array
    {
        return match ($mode) {
            'pattern' => $client->sendPattern(array_merge($payload, [
                'pattern_code' => $snapshot['pattern_code'] ?? null,
                'values' => $snapshot['pattern_values'] ?? [],
            ])),
            'phonebook' => $client->sendPhonebook(array_merge($payload, [
                'phonebook_id' => $snapshot['phonebook_id'] ?? null,
            ])),
            'file' => $client->sendFile(array_merge($payload, [
                'file' => $snapshot['file'] ?? null,
            ])),
            'geo' => $client->sendCountryCity(array_merge($payload, [
                'province' => $snapshot['province'] ?? null,
                'city' => $snapshot['city'] ?? null,
            ])),
            default => $client->sendWebservice($payload),
        };
    }
}
