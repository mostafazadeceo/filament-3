<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Enums;

enum CryptoProviderEnvironment: string
{
    case Sandbox = 'sandbox';
    case Production = 'prod';
}
