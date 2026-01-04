<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Replenishment;

use Haida\FilamentPettyCashIr\Domain\Rules\ReplenishmentRules;
use Haida\FilamentPettyCashIr\Domain\Workflow\WorkflowEngine;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Validation\ValidationException;

class ApproveReplenishment
{
    public function __construct(
        private readonly ReplenishmentRules $rules,
        private readonly WorkflowEngine $workflow,
        private readonly AuditLoggerInterface $auditLogger
    ) {}

    public function handle(PettyCashReplenishment $replenishment, ?int $actorId = null): PettyCashReplenishment
    {
        $this->rules->assertCanApprove($replenishment);

        $decision = $this->workflow->resolveForReplenishment($replenishment);

        if ($decision->requireSeparation && $actorId && (int) $replenishment->requested_by === $actorId) {
            throw ValidationException::withMessages([
                'actor' => 'تأییدکننده نمی‌تواند ثبت‌کننده باشد.',
            ]);
        }

        $stepsRequired = $replenishment->approval_steps_required ?: $decision->stepsRequired;
        $stepsCompleted = (int) $replenishment->approval_steps_completed + 1;
        $history = $replenishment->approval_history ?? [];
        $history[] = [
            'actor_id' => $actorId,
            'approved_at' => now()->toISOString(),
            'step' => $stepsCompleted,
        ];

        $updates = [
            'workflow_rule_id' => $decision->ruleId,
            'approval_steps_required' => $stepsRequired,
            'approval_steps_completed' => $stepsCompleted,
            'approval_history' => $history,
        ];

        if ($stepsCompleted < $stepsRequired) {
            $replenishment->update($updates);
            $this->auditLogger->log($replenishment, $actorId, 'replenishment_approval_step', 'گام تأیید تغذیه', [
                'step' => $stepsCompleted,
                'steps_required' => $stepsRequired,
            ]);
            event(new PettyCashEvent('replenishment.approval_step', $replenishment));

            return $replenishment->refresh();
        }

        $replenishment->update($updates + [
            'status' => PettyCashStatuses::REPLENISHMENT_APPROVED,
            'approved_by' => $actorId,
            'approved_at' => now(),
        ]);

        $this->auditLogger->log($replenishment, $actorId, 'replenishment_approved', 'تأیید تغذیه', [
            'steps_required' => $stepsRequired,
        ]);
        event(new PettyCashEvent('replenishment.approved', $replenishment));

        return $replenishment->refresh();
    }
}
