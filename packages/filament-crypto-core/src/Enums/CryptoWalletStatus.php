<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoCore\Enums;

enum CryptoWalletStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';
}
