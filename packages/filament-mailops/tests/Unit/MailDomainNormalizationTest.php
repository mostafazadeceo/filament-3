<?php

declare(strict_types=1);

use Haida\FilamentMailOps\Models\MailDomain;
use PHPUnit\Framework\TestCase;

final class MailDomainNormalizationTest extends TestCase
{
    public function test_it_normalizes_ascii_domain_name(): void
    {
        $normalized = MailDomain::normalizeDomainName('  ExAmPle.ABRAK.ORG.  ');

        $this->assertSame('example.abrak.org', $normalized);
    }

    public function test_it_returns_null_for_empty_values(): void
    {
        $this->assertNull(MailDomain::normalizeDomainName('   '));
        $this->assertNull(MailDomain::normalizeDomainName(null));
    }
}
