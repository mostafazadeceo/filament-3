<?php

namespace Haida\TenancyDomains\Services;

use Haida\TenancyDomains\Jobs\IssueCertificate;
use Haida\TenancyDomains\Models\SiteDomain;

class SiteDomainService
{
    public function __construct(
        private readonly DomainVerificationService $verificationService,
    ) {}

    public function requestVerification(SiteDomain $domain, ?string $method = null): SiteDomain
    {
        $method = $method ?: config('tenancy-domains.verification.default_method', 'txt');

        $token = $domain->dns_token;
        if (! is_string($token) || $token === '') {
            $token = $this->verificationService->generateDnsToken();
        }

        $domain->forceFill([
            'verification_method' => $method,
            'dns_token' => $token,
            'status' => SiteDomain::STATUS_PENDING,
            'verified_at' => null,
        ])->save();

        return $domain->refresh();
    }

    public function verify(SiteDomain $domain): SiteDomain
    {
        $method = $domain->verification_method ?: config('tenancy-domains.verification.default_method', 'txt');
        $token = $domain->dns_token;
        if (! is_string($token) || $token === '') {
            $token = $this->verificationService->generateDnsToken();
            $domain->dns_token = $token;
        }

        $verified = match ($method) {
            'cname' => $this->verificationService->verifyCname(
                $domain,
                config('tenancy-domains.verification.cname_target', ''),
            ),
            default => $this->verificationService->verifyTxt($domain, $token),
        };

        $domain->last_checked_at = now();

        if ($verified) {
            $domain->status = SiteDomain::STATUS_VERIFIED;
            $domain->verified_at = now();
        } else {
            $domain->status = SiteDomain::STATUS_FAILED;
        }

        $domain->verification_method = $method;
        $domain->save();

        return $domain->refresh();
    }

    public function requestTls(SiteDomain $domain, ?string $provider = null, ?string $mode = null): SiteDomain
    {
        if (! $domain->verified_at) {
            return $domain->refresh();
        }

        $provider = $provider ?: config('tenancy-domains.tls.provider', 'null');
        $mode = $mode ?: config('tenancy-domains.tls.mode', 'dns-01');

        $domain->forceFill([
            'tls_status' => SiteDomain::TLS_STATUS_PENDING,
            'tls_provider' => $provider,
            'tls_mode' => $mode,
            'tls_requested_at' => now(),
            'tls_last_attempted_at' => null,
            'tls_error' => null,
        ])->save();

        IssueCertificate::dispatch($domain->getKey(), $domain->tenant_id, $provider);

        return $domain->refresh();
    }
}
