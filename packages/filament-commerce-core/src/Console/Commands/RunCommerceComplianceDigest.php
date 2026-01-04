<?php

namespace Haida\FilamentCommerceCore\Console\Commands;

use Carbon\Carbon;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCommerceCore\Models\CommerceComplianceDigest;
use Haida\FilamentCommerceCore\Models\CommerceException;
use Illuminate\Console\Command;

class RunCommerceComplianceDigest extends Command
{
    protected $signature = 'commerce:compliance-digest
        {--tenant= : Tenant id or slug}
        {--from= : Start date (ISO or Y-m-d)}
        {--to= : End date (ISO or Y-m-d)}
        {--panel= : Notify-core panel id override}
        {--dry-run : Do not dispatch notifications}';

    protected $description = 'Generate compliance digest summaries and dispatch notify-core triggers.';

    public function handle(): int
    {
        $from = $this->option('from');
        $to = $this->option('to');

        $periodEnd = $to ? Carbon::parse($to) : now();
        $periodStart = $from ? Carbon::parse($from) : $periodEnd->copy()->subDays(7);

        $tenantOption = $this->option('tenant');

        $tenantQuery = Tenant::query();
        if (is_string($tenantOption) && $tenantOption !== '') {
            if (is_numeric($tenantOption)) {
                $tenantQuery->where('id', (int) $tenantOption);
            } else {
                $tenantQuery->where('slug', $tenantOption);
            }
        }

        $tenants = $tenantQuery->get();
        if ($tenants->isEmpty()) {
            $this->warn('No tenants found for compliance digest.');
            return self::SUCCESS;
        }

        $panelId = (string) ($this->option('panel')
            ?? config('filament-commerce-core.compliance.notifications.panel', 'tenant'));
        $event = (string) config('filament-commerce-core.compliance.notifications.digest_event', 'commerce.compliance.digest');
        $minOpen = (int) config('filament-commerce-core.compliance.notifications.min_open', 1);
        $dryRun = (bool) $this->option('dry-run');

        foreach ($tenants as $tenant) {
            TenantContext::setTenant($tenant);

            $baseQuery = CommerceException::query()
                ->where('tenant_id', $tenant->getKey())
                ->whereBetween('created_at', [$periodStart, $periodEnd]);

            $totalCount = (clone $baseQuery)->count();
            $openCount = (clone $baseQuery)->where('status', 'open')->count();
            $resolvedCount = (clone $baseQuery)->where('status', 'resolved')->count();

            $bySeverity = (clone $baseQuery)
                ->selectRaw('severity, count(*) as aggregate')
                ->groupBy('severity')
                ->pluck('aggregate', 'severity')
                ->toArray();

            $byType = (clone $baseQuery)
                ->selectRaw('type, count(*) as aggregate')
                ->groupBy('type')
                ->pluck('aggregate', 'type')
                ->toArray();

            $summary = [
                'period_start' => $periodStart->toIso8601String(),
                'period_end' => $periodEnd->toIso8601String(),
                'total_count' => $totalCount,
                'open_count' => $openCount,
                'resolved_count' => $resolvedCount,
                'by_severity' => $bySeverity,
                'by_type' => $byType,
            ];

            $digest = CommerceComplianceDigest::query()->updateOrCreate([
                'tenant_id' => $tenant->getKey(),
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ], [
                'status' => 'generated',
                'summary' => $summary,
                'created_by_user_id' => auth()->id(),
            ]);

            if ($dryRun) {
                $this->line('Digest generated for tenant '.$tenant->slug.' (dry-run).');
                continue;
            }

            if ($openCount < $minOpen) {
                $this->line('Digest skipped for tenant '.$tenant->slug.' (min open not met).');
                continue;
            }

            if ($panelId !== '' && $event !== '' && class_exists(\Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher::class)) {
                try {
                    app(\Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher::class)
                        ->dispatchForEloquent($panelId, $digest, $event);
                    $this->line('Digest dispatched for tenant '.$tenant->slug.'.');
                } catch (\Throwable $exception) {
                    $this->warn('Digest notify failed for tenant '.$tenant->slug.': '.$exception->getMessage());
                }
            }
        }

        TenantContext::setTenant(null);

        return self::SUCCESS;
    }
}
