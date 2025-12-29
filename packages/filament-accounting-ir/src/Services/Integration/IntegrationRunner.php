<?php

namespace Vendor\FilamentAccountingIr\Services\Integration;

use Illuminate\Support\Facades\DB;
use Vendor\FilamentAccountingIr\Models\IntegrationConnector;
use Vendor\FilamentAccountingIr\Models\IntegrationLog;
use Vendor\FilamentAccountingIr\Models\IntegrationRun;
use Vendor\FilamentAccountingIr\Services\Integration\DTOs\IntegrationResult;

class IntegrationRunner
{
    public function run(IntegrationConnector $connector): IntegrationRun
    {
        if ($connector->tenant) {
            \Filamat\IamSuite\Support\TenantContext::setTenant($connector->tenant);
        }

        $run = IntegrationRun::query()->create([
            'integration_connector_id' => $connector->getKey(),
            'started_at' => now(),
            'status' => 'running',
        ]);

        try {
            $result = $this->executeConnector($connector);

            DB::transaction(function () use ($run, $result): void {
                $run->update([
                    'finished_at' => now(),
                    'status' => $result->success ? 'completed' : 'failed',
                    'summary' => $result->summary,
                ]);

                $this->storeLogs($run, $result);
            });
        } catch (\Throwable $exception) {
            $run->update([
                'finished_at' => now(),
                'status' => 'failed',
                'summary' => ['error' => $exception->getMessage()],
            ]);

            IntegrationLog::query()->create([
                'integration_run_id' => $run->getKey(),
                'level' => 'error',
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $run;
    }

    protected function executeConnector(IntegrationConnector $connector): IntegrationResult
    {
        $registry = new IntegrationRegistry;
        $handler = $registry->resolve($connector->connector_type);

        return $handler->run($connector);
    }

    protected function storeLogs(IntegrationRun $run, IntegrationResult $result): void
    {
        foreach ($result->logs as $log) {
            IntegrationLog::query()->create([
                'integration_run_id' => $run->getKey(),
                'level' => $log['level'] ?? 'info',
                'message' => $log['message'] ?? 'log',
                'context' => $log['context'] ?? null,
            ]);
        }
    }
}
