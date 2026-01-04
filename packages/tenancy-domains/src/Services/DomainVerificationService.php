<?php

namespace Haida\TenancyDomains\Services;

use Haida\TenancyDomains\Models\SiteDomain;

class DomainVerificationService
{
    public function generateDnsToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    public function verifyTxt(SiteDomain $domain, string $expectedToken): bool
    {
        $records = dns_get_record($domain->host, DNS_TXT);
        foreach ($records as $record) {
            $value = $record['txt'] ?? null;
            if (is_string($value) && trim($value) === $expectedToken) {
                return true;
            }
        }

        return false;
    }

    public function verifyCname(SiteDomain $domain, string $expectedTarget): bool
    {
        $records = dns_get_record($domain->host, DNS_CNAME);
        foreach ($records as $record) {
            $value = $record['target'] ?? null;
            if (is_string($value) && rtrim($value, '.') === rtrim($expectedTarget, '.')) {
                return true;
            }
        }

        return false;
    }
}
