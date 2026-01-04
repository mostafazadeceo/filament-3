<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class WalletTransactionCreated extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'wallet.transaction.created';
    }
}
