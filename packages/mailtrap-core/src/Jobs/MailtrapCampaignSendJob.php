<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Jobs;

use Filamat\IamSuite\Support\TenantContext;
use Filamat\IamSuite\Models\Tenant;
use Haida\MailtrapCore\Models\MailtrapCampaign;
use Haida\MailtrapCore\Models\MailtrapCampaignSend;
use Haida\MailtrapCore\Services\MailtrapSendService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class MailtrapCampaignSendJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $timeout = 900;

    public function __construct(
        public int $campaignId,
    ) {}

    public function handle(MailtrapSendService $sendService): void
    {
        $campaign = MailtrapCampaign::query()->with('connection')->find($this->campaignId);
        if (! $campaign || ! $campaign->connection) {
            return;
        }

        $previousTenant = TenantContext::getTenant();
        $previousBypass = TenantContext::shouldBypass();
        $tenant = Tenant::query()->find($campaign->tenant_id);
        if ($tenant) {
            TenantContext::setTenant($tenant);
            TenantContext::bypass(false);
        } else {
            TenantContext::bypass(true);
        }

        try {
            if (in_array($campaign->status, ['draft', 'scheduled'], true)) {
                $campaign->update([
                    'status' => 'sending',
                    'started_at' => $campaign->started_at ?? now(),
                ]);
            }

            if (! in_array($campaign->status, ['sending'], true)) {
                return;
            }

            $pending = MailtrapCampaignSend::query()
                ->where('tenant_id', $campaign->tenant_id)
                ->where('campaign_id', $campaign->getKey())
                ->where('status', 'pending')
                ->limit(50)
                ->get();

            foreach ($pending as $send) {
                try {
                    $payload = [
                        'from_email' => $campaign->from_email,
                        'from_name' => $campaign->from_name,
                        'html' => $campaign->html_body,
                        'text' => $campaign->text_body,
                        'to_name' => $send->name,
                        'category' => $campaign->name,
                        'custom_variables' => [
                            'campaign_id' => $campaign->getKey(),
                            'recipient' => $send->email,
                        ],
                    ];

                    $response = $sendService->sendSimple(
                        $campaign->connection,
                        $send->email,
                        $campaign->subject,
                        $campaign->text_body ?? '',
                        $payload
                    );

                    $send->update([
                        'status' => 'sent',
                        'provider_message_id' => $response['message_id'] ?? null,
                        'response' => $response,
                        'sent_at' => now(),
                        'error_message' => null,
                    ]);
                } catch (Throwable $exception) {
                    $send->update([
                        'status' => 'failed',
                        'error_message' => $exception->getMessage(),
                        'sent_at' => now(),
                    ]);
                }
            }

            $remaining = MailtrapCampaignSend::query()
                ->where('tenant_id', $campaign->tenant_id)
                ->where('campaign_id', $campaign->getKey())
                ->where('status', 'pending')
                ->count();

            $stats = [
                'total' => MailtrapCampaignSend::query()->where('campaign_id', $campaign->getKey())->count(),
                'sent' => MailtrapCampaignSend::query()->where('campaign_id', $campaign->getKey())->where('status', 'sent')->count(),
                'failed' => MailtrapCampaignSend::query()->where('campaign_id', $campaign->getKey())->where('status', 'failed')->count(),
                'pending' => $remaining,
            ];

            if ($remaining > 0) {
                $campaign->update(['stats' => $stats]);
                self::dispatch($campaign->getKey())->delay(now()->addSeconds(5))->onQueue((string) config('mailtrap-core.queue', 'default'));
                return;
            }

            $finalStatus = $stats['sent'] > 0
                ? 'sent'
                : ($stats['failed'] > 0 ? 'failed' : 'sent');

            $campaign->update([
                'status' => $finalStatus,
                'finished_at' => now(),
                'stats' => $stats,
            ]);
        } finally {
            TenantContext::setTenant($previousTenant);
            TenantContext::bypass($previousBypass);
        }
    }
}
