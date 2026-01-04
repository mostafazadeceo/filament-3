<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Replenishment;

use Haida\FilamentPettyCashIr\Domain\Rules\ReplenishmentRules;
use Haida\FilamentPettyCashIr\Domain\Workflow\WorkflowEngine;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;

class SubmitReplenishment
{
    public function __construct(
        private readonly ReplenishmentRules $rules,
        private readonly WorkflowEngine $workflow,
        private readonly AuditLoggerInterface $auditLogger
    ) {}

    public function handle(PettyCashReplenishment $replenishment, ?int $actorId = null): PettyCashReplenishment
    {
        $this->rules->assertCanSubmit($replenishment);

        $decision = $this->workflow->resolveForReplenishment($replenishment);

        $replenishment->update([
            'status' => PettyCashStatuses::REPLENISHMENT_SUBMITTED,
            'requested_by' => $replenishment->requested_by ?: $actorId,
            'workflow_rule_id' => $decision->ruleId,
            'approval_steps_required' => $decision->stepsRequired,
            'approval_steps_completed' => 0,
            'approval_history' => [],
        ]);

        $this->auditLogger->log($replenishment, $actorId, 'replenishment_submitted', 'ارسال درخواست تغذیه');
        event(new PettyCashEvent('replenishment.submitted', $replenishment));

        return $replenishment->refresh();
    }
}
