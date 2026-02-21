<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentChat\Policies\ChatConnectionPolicy;
use Haida\FilamentChat\Policies\ChatUserLinkPolicy;

final class ChatCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'chat',
            self::permissions(),
            [
                'tenant_owner_manage' => true,
            ],
            [
                'plan' => [
                    'max_users' => '',
                    'max_channels' => '',
                    'max_private_rooms' => '',
                ],
                'trial' => [
                    'max_users' => '',
                    'max_channels' => '',
                    'max_private_rooms' => '',
                ],
            ],
            [
                ChatConnectionPolicy::class,
                ChatUserLinkPolicy::class,
            ],
            [
                'chat' => 'اتاق گفتگو',
                'chat_connections' => 'اتصال های چت',
                'chat_users' => 'کاربران چت',
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
            'chat.connection.view',
            'chat.connection.manage',
            'chat.user.view',
            'chat.user.manage',
            'chat.sync',
        ];
    }
}
