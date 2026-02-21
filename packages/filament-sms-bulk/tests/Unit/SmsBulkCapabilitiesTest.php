<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Tests\Unit;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\SmsBulk\Support\SmsBulkCapabilities;
use Haida\SmsBulk\Support\SmsBulkPermissionLabels;
use Haida\SmsBulk\Tests\TestCase;

class SmsBulkCapabilitiesTest extends TestCase
{
    public function test_capability_registration_and_labels_exist(): void
    {
        $registry = app(CapabilityRegistryInterface::class);
        SmsBulkCapabilities::register($registry);

        $permissions = SmsBulkCapabilities::permissions();

        $this->assertContains('sms-bulk.campaign.submit', $permissions);
        $this->assertContains('sms-bulk.suppression.manage', $permissions);

        $labels = SmsBulkPermissionLabels::labels();
        $this->assertSame('مشاهده ماژول پیامک بالک', $labels['fa']['sms-bulk.view']);
        $this->assertSame('View SMS Bulk module', $labels['en']['sms-bulk.view']);
        $this->assertSame('عرض وحدة الرسائل بالجملة', $labels['ar']['sms-bulk.view']);
    }
}
