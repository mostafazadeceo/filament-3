<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Console\Commands;

use Filamat\IamSuite\Models\IamAiReport;
use Filamat\IamSuite\Models\WebhookDelivery;
use Illuminate\Console\Command;

class IamAutomationPruneCommand extends Command
{
    protected $signature = 'iam:automation:prune';

    protected $description = 'Prune automation webhook deliveries and AI reports based on retention config.';

    public function handle(): int
    {
        $deliveryDays = (int) config('filamat-iam.automation.retention_days.deliveries', 30);
        $reportDays = (int) config('filamat-iam.automation.retention_days.reports', 90);

        $deliveryCutoff = now()->subDays($deliveryDays);
        $reportCutoff = now()->subDays($reportDays);

        $deliveries = WebhookDelivery::query()
            ->where('created_at', '<', $deliveryCutoff)
            ->whereHas('webhook', fn ($builder) => $builder->where('type', 'automation'))
            ->delete();

        $reports = IamAiReport::query()
            ->where('created_at', '<', $reportCutoff)
            ->delete();

        $this->info('Pruned deliveries: '.$deliveries);
        $this->info('Pruned reports: '.$reports);

        return self::SUCCESS;
    }
}
