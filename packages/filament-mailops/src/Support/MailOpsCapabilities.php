<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentMailOps\Policies\MailAliasPolicy;
use Haida\FilamentMailOps\Policies\MailDomainPolicy;
use Haida\FilamentMailOps\Policies\MailInboundMessagePolicy;
use Haida\FilamentMailOps\Policies\MailMailboxPolicy;
use Haida\FilamentMailOps\Policies\MailOutboundMessagePolicy;

final class MailOpsCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'mailops',
            self::permissions(),
            [
                'mailops' => true,
            ],
            [],
            [
                MailDomainPolicy::class,
                MailMailboxPolicy::class,
                MailAliasPolicy::class,
                MailOutboundMessagePolicy::class,
                MailInboundMessagePolicy::class,
            ],
            [
                'mailops' => 'ایمیل',
                'mailops_domains' => 'دامنه‌های ایمیل',
                'mailops_mailboxes' => 'صندوق‌های ایمیل',
                'mailops_aliases' => 'نام‌های مستعار ایمیل',
                'mailops_outbound' => 'ارسال‌های ایمیل',
                'mailops_inbound' => 'پیام‌های دریافتی',
            ]
        );

        self::$registered = true;
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'mailops.domain.view',
            'mailops.domain.manage',
            'mailops.mailbox.view',
            'mailops.mailbox.manage',
            'mailops.alias.view',
            'mailops.alias.manage',
            'mailops.outbound.view',
            'mailops.outbound.send',
            'mailops.inbound.view',
            'mailops.inbound.sync',
            'mailops.settings.manage',
        ];
    }
}
