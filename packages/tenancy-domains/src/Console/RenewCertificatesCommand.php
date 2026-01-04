<?php

declare(strict_types=1);

namespace Haida\TenancyDomains\Console;

use Haida\TenancyDomains\Jobs\IssueCertificate;
use Haida\TenancyDomains\Models\SiteDomain;
use Illuminate\Console\Command;

class RenewCertificatesCommand extends Command
{
    protected $signature = 'tenancy-domains:renew-certificates {--dry-run}';

    protected $description = 'Queue certificate renewals for expiring custom domains.';

    public function handle(): int
    {
        if (! config('tenancy-domains.tls.enabled', false)) {
            $this->warn('TLS automation is disabled.');

            return self::SUCCESS;
        }

        $threshold = now()->addDays((int) config('tenancy-domains.tls.renew_before_days', 30));

        $query = SiteDomain::query()
            ->whereNotNull('verified_at')
            ->where('tls_status', SiteDomain::TLS_STATUS_ISSUED)
            ->whereNotNull('tls_expires_at')
            ->where('tls_expires_at', '<=', $threshold);

        $count = $query->count();

        if ($this->option('dry-run')) {
            $this->info("{$count} certificate(s) ready for renewal.");

            return self::SUCCESS;
        }

        $query->chunkById(50, function ($domains): void {
            foreach ($domains as $domain) {
                IssueCertificate::dispatch($domain->getKey(), $domain->tenant_id, $domain->tls_provider);
            }
        });

        $this->info("Queued {$count} certificate(s) for renewal.");

        return self::SUCCESS;
    }
}
