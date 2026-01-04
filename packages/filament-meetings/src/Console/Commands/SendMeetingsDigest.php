<?php

namespace Haida\FilamentMeetings\Console\Commands;

use Haida\FilamentMeetings\Services\MeetingFollowUpService;
use Illuminate\Console\Command;

class SendMeetingsDigest extends Command
{
    protected $signature = 'meetings:send-weekly-digest {--tenant=}';

    protected $description = 'ارسال جمع‌بندی هفتگی اقدام‌های جلسات';

    public function handle(MeetingFollowUpService $service): int
    {
        $tenantId = $this->option('tenant');
        $tenantId = $tenantId !== null ? (int) $tenantId : null;

        $count = $service->sendWeeklyDigest($tenantId);

        $this->info(sprintf('Weekly meeting digest dispatched for %d action items.', $count));

        return self::SUCCESS;
    }
}
