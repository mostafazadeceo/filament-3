<?php

namespace Haida\SiteBuilderCore\Services;

use Haida\SiteBuilderCore\Enums\SiteStatus;
use Haida\SiteBuilderCore\Models\Site;
use Haida\SiteBuilderCore\Models\SitePublishHistory;
use Illuminate\Support\Facades\DB;

class SitePublisher
{
    public function publish(Site $site, ?int $actorUserId = null): Site
    {
        return $this->transition($site, SiteStatus::Published, $actorUserId);
    }

    public function preview(Site $site, ?int $actorUserId = null): Site
    {
        return $this->transition($site, SiteStatus::Preview, $actorUserId);
    }

    public function disable(Site $site, ?int $actorUserId = null): Site
    {
        return $this->transition($site, SiteStatus::Disabled, $actorUserId);
    }

    public function resetToDraft(Site $site, ?int $actorUserId = null): Site
    {
        return $this->transition($site, SiteStatus::Draft, $actorUserId);
    }

    protected function transition(Site $site, SiteStatus $target, ?int $actorUserId = null): Site
    {
        if ($site->status === $target->value) {
            return $site;
        }

        return DB::transaction(function () use ($site, $target, $actorUserId): Site {
            $fromStatus = $site->status;

            $site->status = $target->value;

            if ($target === SiteStatus::Published) {
                $site->published_at = now();
            }

            if ($target !== SiteStatus::Published && $site->published_at) {
                $site->published_at = null;
            }

            $site->save();

            SitePublishHistory::query()->create([
                'tenant_id' => $site->tenant_id,
                'site_id' => $site->getKey(),
                'from_status' => $fromStatus,
                'to_status' => $target->value,
                'actor_user_id' => $actorUserId,
                'metadata' => [
                    'transitioned_at' => now()->toISOString(),
                ],
            ]);

            return $site;
        });
    }
}
