<?php

declare(strict_types=1);

use Haida\FilamentMailOps\Models\MailMailbox;
use Tests\TestCase;

final class MailMailboxConnectionSettingsTest extends TestCase
{
    public function test_it_applies_smtp_verify_tls_default_from_config(): void
    {
        config()->set('filament-mailops.smtp.verify_tls', false);
        config()->set('filament-mailops.smtp.host', '');

        $settings = MailMailbox::normalizeConnectionSettings([], 'abrak.org');

        $this->assertSame('mail.abrak.org', $settings['smtp_host']);
        $this->assertFalse($settings['smtp_verify_tls']);
    }

    public function test_it_casts_smtp_verify_tls_to_boolean(): void
    {
        config()->set('filament-mailops.smtp.verify_tls', true);

        $settings = MailMailbox::normalizeConnectionSettings([
            'smtp_verify_tls' => 0,
            'smtp_host' => 'mail.abrak.org',
            'smtp_port' => '587',
            'smtp_encryption' => 'tls',
        ], 'abrak.org');

        $this->assertFalse($settings['smtp_verify_tls']);
        $this->assertSame(587, $settings['smtp_port']);
    }
}
