<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Console\Commands;

use Filamat\IamSuite\Services\Automation\IamAiAuditRunner;
use Illuminate\Console\Command;

class IamAiAuditRunCommand extends Command
{
    protected $signature = 'iam:ai-audit:run';

    protected $description = 'Run IAM AI audit summary and dispatch to n8n connectors.';

    public function handle(IamAiAuditRunner $runner): int
    {
        $results = $runner->run();

        $this->info('AI audit runs: '.count($results));
        foreach ($results as $result) {
            $this->line(sprintf(
                'tenant=%s run=%s delivery=%s status=%s',
                $result['tenant_id'] ?? 'n/a',
                $result['run_id'] ?? 'n/a',
                $result['delivery_id'] ?? 'n/a',
                $result['status'] ?? 'n/a'
            ));
        }

        return self::SUCCESS;
    }
}
