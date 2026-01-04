<?php

namespace Haida\FilamentLoyaltyClub\Console\Commands;

use Haida\FilamentLoyaltyClub\Services\LoyaltyRetentionService;
use Illuminate\Console\Command;

class LoyaltyRetentionPruneCommand extends Command
{
    protected $signature = 'loyalty:retention:prune';

    protected $description = 'Prune loyalty audit/events/fraud/campaign logs based on retention config.';

    public function handle(): int
    {
        $result = app(LoyaltyRetentionService::class)->prune();

        $this->info('Pruned audits: '.$result['audits']);
        $this->info('Pruned events: '.$result['events']);
        $this->info('Pruned fraud signals: '.$result['frauds']);
        $this->info('Pruned campaign dispatches: '.$result['campaign_dispatches']);

        return self::SUCCESS;
    }
}
