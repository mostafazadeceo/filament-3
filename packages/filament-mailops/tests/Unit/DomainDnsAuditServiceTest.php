<?php

declare(strict_types=1);

use Haida\FilamentMailOps\Services\DomainDnsAuditService;
use PHPUnit\Framework\TestCase;

final class DomainDnsAuditServiceTest extends TestCase
{
    public function test_it_marks_snapshot_without_records_as_unknown(): void
    {
        $service = new DomainDnsAuditService;
        $audit = $service->evaluate([]);

        $this->assertSame('unknown', $audit['status']);
        $this->assertSame(0, $audit['score']);
        $this->assertNotEmpty($audit['issues']);
    }

    public function test_it_marks_critical_when_essential_records_are_missing(): void
    {
        $service = new DomainDnsAuditService;
        $audit = $service->evaluate([
            'dns_mx' => '10 mail.abrak.org',
            'dns_spf' => null,
            'dns_dkim' => null,
            'dns_dmarc' => null,
        ]);

        $this->assertSame('critical', $audit['status']);
        $this->assertLessThan(60, $audit['score']);
        $this->assertNotEmpty($audit['issues']);
    }

    public function test_it_marks_healthy_when_all_records_exist(): void
    {
        $service = new DomainDnsAuditService;
        $audit = $service->evaluate([
            'dns_mx' => '10 mail.abrak.org',
            'dns_spf' => 'v=spf1 mx -all',
            'dns_dkim' => 'v=DKIM1; k=rsa; p=ABC123',
            'dns_dmarc' => 'v=DMARC1; p=none',
            'dns_dmarc_report' => 'v=DMARC1; p=none; rua=mailto:dmarc@abrak.org',
            'dns_tlsa' => '3 1 1 123456',
            'dns_autoconfig' => 'mail.abrak.org',
        ]);

        $this->assertSame('healthy', $audit['status']);
        $this->assertGreaterThanOrEqual(95, $audit['score']);
        $this->assertSame([], $audit['issues']);
    }
}
