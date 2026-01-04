<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Services;

use Haida\MailtrapCore\Jobs\MailtrapCampaignSendJob;
use Haida\MailtrapCore\Models\MailtrapAudienceContact;
use Haida\MailtrapCore\Models\MailtrapCampaign;
use Haida\MailtrapCore\Models\MailtrapCampaignSend;

class MailtrapCampaignService
{
    public function dispatchWithSchedule(MailtrapCampaign $campaign): string
    {
        if (! $campaign->audience_id) {
            return 'no-audience';
        }

        if ($campaign->scheduled_at && $campaign->scheduled_at->isFuture()) {
            $campaign->update(['status' => 'scheduled']);
            $this->prepareCampaignSends($campaign);

            MailtrapCampaignSendJob::dispatch($campaign->getKey())
                ->delay($campaign->scheduled_at)
                ->onQueue((string) config('mailtrap-core.queue', 'default'));

            return 'scheduled';
        }

        $this->dispatchCampaign($campaign);

        return 'dispatched';
    }

    public function prepareCampaignSends(MailtrapCampaign $campaign): int
    {
        if (! $campaign->audience_id) {
            return 0;
        }

        $contacts = MailtrapAudienceContact::query()
            ->where('tenant_id', $campaign->tenant_id)
            ->where('audience_id', $campaign->audience_id)
            ->where('status', 'subscribed')
            ->get();

        $created = 0;
        foreach ($contacts as $contact) {
            $exists = MailtrapCampaignSend::query()
                ->where('tenant_id', $campaign->tenant_id)
                ->where('campaign_id', $campaign->getKey())
                ->where('email', $contact->email)
                ->exists();

            if ($exists) {
                continue;
            }

            MailtrapCampaignSend::query()->create([
                'tenant_id' => $campaign->tenant_id,
                'campaign_id' => $campaign->getKey(),
                'audience_contact_id' => $contact->getKey(),
                'email' => $contact->email,
                'name' => $contact->name,
                'status' => 'pending',
            ]);
            $created++;
        }

        return $created;
    }

    public function dispatchCampaign(MailtrapCampaign $campaign): void
    {
        $this->prepareCampaignSends($campaign);

        MailtrapCampaignSendJob::dispatch($campaign->getKey())
            ->onQueue((string) config('mailtrap-core.queue', 'default'));
    }
}
