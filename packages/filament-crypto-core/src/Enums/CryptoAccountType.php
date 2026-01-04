<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoCore\Enums;

enum CryptoAccountType: string
{
    case Asset = 'asset';
    case Liability = 'liability';
    case Revenue = 'revenue';
    case Expense = 'expense';
}
