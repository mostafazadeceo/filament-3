<?php

namespace Haida\FilamentPettyCashIr\Services;

use Haida\FilamentPettyCashIr\Application\UseCases\Expense\ApproveExpense;
use Haida\FilamentPettyCashIr\Application\UseCases\Expense\PostExpense;
use Haida\FilamentPettyCashIr\Application\UseCases\Expense\RejectExpense;
use Haida\FilamentPettyCashIr\Application\UseCases\Expense\ReverseExpense;
use Haida\FilamentPettyCashIr\Application\UseCases\Expense\SubmitExpense;
use Haida\FilamentPettyCashIr\Application\UseCases\Replenishment\ApproveReplenishment;
use Haida\FilamentPettyCashIr\Application\UseCases\Replenishment\PostReplenishment;
use Haida\FilamentPettyCashIr\Application\UseCases\Replenishment\RejectReplenishment;
use Haida\FilamentPettyCashIr\Application\UseCases\Replenishment\ReverseReplenishment;
use Haida\FilamentPettyCashIr\Application\UseCases\Replenishment\SubmitReplenishment;
use Haida\FilamentPettyCashIr\Application\UseCases\Settlement\ApproveSettlement;
use Haida\FilamentPettyCashIr\Application\UseCases\Settlement\PostSettlement;
use Haida\FilamentPettyCashIr\Application\UseCases\Settlement\ReverseSettlement;
use Haida\FilamentPettyCashIr\Application\UseCases\Settlement\SubmitSettlement;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;

class PettyCashPostingService
{
    public function __construct(
        private readonly SubmitExpense $submitExpense,
        private readonly ApproveExpense $approveExpense,
        private readonly RejectExpense $rejectExpense,
        private readonly PostExpense $postExpense,
        private readonly ReverseExpense $reverseExpense,
        private readonly SubmitReplenishment $submitReplenishment,
        private readonly ApproveReplenishment $approveReplenishment,
        private readonly RejectReplenishment $rejectReplenishment,
        private readonly PostReplenishment $postReplenishment,
        private readonly ReverseReplenishment $reverseReplenishment,
        private readonly SubmitSettlement $submitSettlement,
        private readonly ApproveSettlement $approveSettlement,
        private readonly PostSettlement $postSettlement,
        private readonly ReverseSettlement $reverseSettlement
    ) {}

    public function submitExpense(PettyCashExpense $expense, ?int $actorId = null): PettyCashExpense
    {
        return $this->submitExpense->handle($expense, $actorId);
    }

    public function approveExpense(PettyCashExpense $expense, ?int $actorId = null): PettyCashExpense
    {
        return $this->approveExpense->handle($expense, $actorId);
    }

    public function rejectExpense(PettyCashExpense $expense, ?int $actorId = null): PettyCashExpense
    {
        return $this->rejectExpense->handle($expense, $actorId);
    }

    public function postExpense(PettyCashExpense $expense, ?int $actorId = null, ?string $idempotencyKey = null): PettyCashExpense
    {
        return $this->postExpense->handle($expense, $actorId, $idempotencyKey);
    }

    public function reverseExpense(
        PettyCashExpense $expense,
        ?int $actorId = null,
        ?string $idempotencyKey = null,
        ?string $reason = null
    ): PettyCashExpense {
        return $this->reverseExpense->handle($expense, $actorId, $idempotencyKey, $reason);
    }

    public function submitReplenishment(PettyCashReplenishment $replenishment, ?int $actorId = null): PettyCashReplenishment
    {
        return $this->submitReplenishment->handle($replenishment, $actorId);
    }

    public function approveReplenishment(PettyCashReplenishment $replenishment, ?int $actorId = null): PettyCashReplenishment
    {
        return $this->approveReplenishment->handle($replenishment, $actorId);
    }

    public function rejectReplenishment(PettyCashReplenishment $replenishment, ?int $actorId = null): PettyCashReplenishment
    {
        return $this->rejectReplenishment->handle($replenishment, $actorId);
    }

    public function postReplenishment(PettyCashReplenishment $replenishment, ?int $actorId = null, ?string $idempotencyKey = null): PettyCashReplenishment
    {
        return $this->postReplenishment->handle($replenishment, $actorId, $idempotencyKey);
    }

    public function reverseReplenishment(
        PettyCashReplenishment $replenishment,
        ?int $actorId = null,
        ?string $idempotencyKey = null,
        ?string $reason = null
    ): PettyCashReplenishment {
        return $this->reverseReplenishment->handle($replenishment, $actorId, $idempotencyKey, $reason);
    }

    public function submitSettlement(PettyCashSettlement $settlement, ?int $actorId = null): PettyCashSettlement
    {
        return $this->submitSettlement->handle($settlement, $actorId);
    }

    public function approveSettlement(PettyCashSettlement $settlement, ?int $actorId = null): PettyCashSettlement
    {
        return $this->approveSettlement->handle($settlement, $actorId);
    }

    public function postSettlement(PettyCashSettlement $settlement, ?int $actorId = null, ?string $idempotencyKey = null): PettyCashSettlement
    {
        return $this->postSettlement->handle($settlement, $actorId, $idempotencyKey);
    }

    public function reverseSettlement(
        PettyCashSettlement $settlement,
        ?int $actorId = null,
        ?string $idempotencyKey = null,
        ?string $reason = null
    ): PettyCashSettlement {
        return $this->reverseSettlement->handle($settlement, $actorId, $idempotencyKey, $reason);
    }
}
