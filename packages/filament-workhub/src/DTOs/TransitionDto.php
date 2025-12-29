<?php

namespace Haida\FilamentWorkhub\DTOs;

use Haida\FilamentWorkhub\Models\Transition;

final class TransitionDto
{
    public function __construct(
        public int $id,
        public int $workflowId,
        public int $fromStatusId,
        public int $toStatusId,
    ) {}

    public static function fromModel(Transition $transition): self
    {
        return new self(
            $transition->getKey(),
            (int) $transition->workflow_id,
            (int) $transition->from_status_id,
            (int) $transition->to_status_id,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'workflow_id' => $this->workflowId,
            'from_status_id' => $this->fromStatusId,
            'to_status_id' => $this->toStatusId,
        ];
    }
}
