<?php

namespace Haida\FilamentRelograde\Enums;

enum Environment: string
{
    case Sandbox = 'sandbox';
    case Production = 'production';

    public static function fromWebhookState(?string $state): ?self
    {
        if ($state === null) {
            return null;
        }

        return match ($state) {
            'sandbox' => self::Sandbox,
            'live', 'production' => self::Production,
            default => null,
        };
    }
}
