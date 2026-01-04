<?php

namespace Haida\FilamentThreeCx\Console\Commands;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Services\ThreeCxSyncService;
use Illuminate\Console\Command;

class ThreeCxSyncCommand extends Command
{
    protected $signature = 'threecx:sync {instance?} {--entity=all}';

    protected $description = 'Sync 3CX entities (contacts, call_history, chat_history).';

    public function handle(ThreeCxSyncService $syncService): int
    {
        $entity = (string) $this->option('entity');
        $instanceArg = $this->argument('instance');

        $instances = $instanceArg
            ? ThreeCxInstance::query()->where('id', $instanceArg)->get()
            : ThreeCxInstance::query()->get();

        if ($instances->isEmpty()) {
            $this->warn('هیچ اتصالی یافت نشد.');

            return self::SUCCESS;
        }

        foreach ($instances as $instance) {
            if (! $instance->xapi_enabled) {
                $this->warn("XAPI برای اتصال {$instance->getKey()} غیرفعال است.");

                continue;
            }

            $previousTenant = TenantContext::getTenant();

            try {
                TenantContext::setTenant($instance->tenant);

                $count = match ($entity) {
                    'contacts' => $syncService->syncContacts($instance),
                    'call_history' => $syncService->syncCallHistory($instance),
                    'chat_history' => $syncService->syncChatHistory($instance),
                    default => $this->syncAll($syncService, $instance),
                };

                $this->info("همگام‌سازی {$instance->getKey()} انجام شد. ({$count})");
            } catch (\Throwable $exception) {
                $this->error("خطا در همگام‌سازی {$instance->getKey()}: {$exception->getMessage()}");
            } finally {
                TenantContext::setTenant($previousTenant);
            }
        }

        return self::SUCCESS;
    }

    protected function syncAll(ThreeCxSyncService $syncService, ThreeCxInstance $instance): int
    {
        $count = 0;
        $count += $syncService->syncContacts($instance);
        $count += $syncService->syncCallHistory($instance);
        $count += $syncService->syncChatHistory($instance);

        return $count;
    }
}
