<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\MailtrapCore\Policies\MailtrapAudiencePolicy;
use Haida\MailtrapCore\Policies\MailtrapCampaignPolicy;
use Haida\MailtrapCore\Policies\MailtrapConnectionPolicy;
use Haida\MailtrapCore\Policies\MailtrapInboxPolicy;
use Haida\MailtrapCore\Policies\MailtrapMessagePolicy;
use Haida\MailtrapCore\Policies\MailtrapOfferPolicy;
use Haida\MailtrapCore\Policies\MailtrapSendingDomainPolicy;
use Haida\MailtrapCore\Policies\MailtrapSingleSendPolicy;

final class MailtrapCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'mailtrap',
            self::permissions(),
            [
                'mailtrap' => true,
            ],
            [],
            [
                MailtrapConnectionPolicy::class,
                MailtrapInboxPolicy::class,
                MailtrapMessagePolicy::class,
                MailtrapSendingDomainPolicy::class,
                MailtrapOfferPolicy::class,
                MailtrapAudiencePolicy::class,
                MailtrapCampaignPolicy::class,
                MailtrapSingleSendPolicy::class,
            ],
            [
                'mailtrap' => 'Mailtrap',
                'mailtrap_connections' => 'اتصال‌های Mailtrap',
                'mailtrap_inboxes' => 'این‌باکس‌های Mailtrap',
                'mailtrap_messages' => 'پیام‌های Mailtrap',
                'mailtrap_domains' => 'دامنه‌های ارسال Mailtrap',
                'mailtrap_offers' => 'پکیج‌های فروش Mailtrap',
                'mailtrap_audiences' => 'مخاطبان Mailtrap',
                'mailtrap_campaigns' => 'کمپین‌های ایمیلی',
                'mailtrap_single_sends' => 'ارسال‌های تکی',
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
            'mailtrap.connection.view',
            'mailtrap.connection.manage',
            'mailtrap.inbox.view',
            'mailtrap.inbox.sync',
            'mailtrap.message.view',
            'mailtrap.domain.view',
            'mailtrap.domain.sync',
            'mailtrap.domain.manage',
            'mailtrap.offer.view',
            'mailtrap.offer.manage',
            'mailtrap.send.test',
            'mailtrap.send.single',
            'mailtrap.inbox.manage',
            'mailtrap.audience.view',
            'mailtrap.audience.manage',
            'mailtrap.campaign.view',
            'mailtrap.campaign.manage',
            'mailtrap.campaign.send',
        ];
    }
}
