<?php

declare(strict_types=1);

namespace Haida\TenancyDomains\Jobs;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\TenancyDomains\Models\SiteDomain;
use Haida\TenancyDomains\Services\CertificateManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class IssueCertificate implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries;

    public function __construct(
        public int $domainId,
        public ?int $tenantId = null,
        public ?string $providerKey = null,
    ) {
        $this->tries = (int) config('tenancy-domains.tls.retry.tries', 3);
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        $backoff = (array) config('tenancy-domains.tls.retry.backoff_seconds', [30, 60, 120]);

        return $backoff === [] ? [30] : $backoff;
    }

    public function handle(CertificateManager $manager): void
    {
        $previousTenant = TenantContext::getTenant();
        $previousBypass = TenantContext::shouldBypass();

        if ($this->tenantId) {
            $tenant = Tenant::query()->find($this->tenantId);
            if ($tenant) {
                TenantContext::setTenant($tenant);
            }
            TenantContext::bypass(false);
        } else {
            TenantContext::bypass(true);
        }

        try {
            $domain = SiteDomain::query()->find($this->domainId);
            if (! $domain || ! $domain->verified_at) {
                return;
            }

            $domain->forceFill([
                'tls_last_attempted_at' => now(),
            ])->save();

            $result = $manager->issue($domain, $this->providerKey);

            if ($result->issued) {
                $domain->forceFill([
                    'tls_status' => SiteDomain::TLS_STATUS_ISSUED,
                    'tls_issued_at' => $result->issuedAt,
                    'tls_expires_at' => $result->expiresAt,
                    'tls_error' => null,
                ])->save();
            } else {
                $domain->forceFill([
                    'tls_status' => SiteDomain::TLS_STATUS_FAILED,
                    'tls_error' => $result->error,
                ])->save();
            }
        } catch (Throwable $exception) {
            $domain = SiteDomain::query()->find($this->domainId);
            if ($domain) {
                $domain->forceFill([
                    'tls_status' => SiteDomain::TLS_STATUS_FAILED,
                    'tls_error' => $exception->getMessage(),
                    'tls_last_attempted_at' => now(),
                ])->save();
            }

            throw $exception;
        } finally {
            TenantContext::setTenant($previousTenant);
            TenantContext::bypass($previousBypass);
        }
    }
}
