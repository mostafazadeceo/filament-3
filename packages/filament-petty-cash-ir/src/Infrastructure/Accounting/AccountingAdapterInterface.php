<?php

namespace Haida\FilamentPettyCashIr\Infrastructure\Accounting;

use Haida\FilamentPettyCashIr\Application\DTO\PostingResult;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;

interface AccountingAdapterInterface
{
    public function postExpense(PettyCashExpense $expense, PettyCashFund $fund): PostingResult;

    public function reverseExpense(PettyCashExpense $expense): PostingResult;

    public function postReplenishment(PettyCashReplenishment $replenishment, PettyCashFund $fund): PostingResult;

    public function reverseReplenishment(PettyCashReplenishment $replenishment): PostingResult;

    public function postSettlement(PettyCashSettlement $settlement): PostingResult;

    public function reverseSettlement(PettyCashSettlement $settlement): PostingResult;
}
