<?php

namespace Haida\FilamentPettyCashIr\Domain\Workflow;

final class WorkflowDecision
{
    public function __construct(
        public readonly ?int $ruleId,
        public readonly int $stepsRequired,
        public readonly bool $requireSeparation,
        public readonly ?bool $requireReceipt
    ) {}
}
