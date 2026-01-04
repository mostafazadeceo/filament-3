<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoCore\Enums;

enum CryptoWalletMode: string
{
    case Custodial = 'custodial';
    case WatchOnly = 'watch_only';
    case Provider = 'provider';
}
